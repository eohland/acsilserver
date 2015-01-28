#include "Synchro.h"
Synchro* Synchro::_instance = 0;

Synchro::Synchro(const char *token, char *folder, char *URL)
	: folder(folder), URL(URL)
{
	_instance = this;
	this->_sysWatcher = new QFileSystemWatcher();
	connect(_instance->_sysWatcher, SIGNAL(directoryChanged(QString)), _instance, SLOT(directoryUpdated(QString)));
	connect(_instance->_sysWatcher, SIGNAL(fileChanged(QString)), _instance, SLOT(fileUpdated(QString)));
	this->xtoken = token;

}

Synchro::~Synchro()
{
}

bool replace(std::string& str, const std::string& from, const std::string& to) {
	size_t start_pos = str.find(from);
	if (start_pos == std::string::npos)
		return false;
	str.replace(start_pos, from.length(), to);
	return true;
}

void Synchro::GetJSON()
{
	try {
		if (oldparse.getJSON().empty() != true)
		{
			char acsjson[1024];
			strcpy(acsjson, this->folder);
			strcat(acsjson, ".acsilserver");
			QFile txt(acsjson);

			txt.open(QIODevice::ReadWrite | QIODevice::Text);
			txt.write(myparse.getJSON().c_str());
			txt.close();

			oldparse.parse(myparse.getJSON());
		}
		else
		{
			char acsjson[1024];
			strcpy(acsjson, this->folder);
			strcat(acsjson, ".acsilserver");
			QFile txt(acsjson);
			if (txt.exists() == true)
			{

				std::string	line;
				std::ifstream myfile(acsjson);
				std::getline(myfile, line);
				while (line.find("-0001-11-30T00:00:00+0009") != std::string::npos)
					replace(line, "-0001-11-30T00:00:00+0009", "2014-10-18T14:24:39+0200");
				oldparse.parse(line);
			}
		}


		char toto[1024];
		strcpy(toto, this->URL);
		strcat(toto, "app_dev.php/service/1/op/listAll");
		m_network = new QNetworkAccessManager();
		QNetworkRequest request;
		QString header = "";

		request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));
		request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
		request.setRawHeader("Accept", "application/json");
		request.setRawHeader("Accept-encoding", "identity");

		request.setUrl(QUrl(toto));
		QNetworkReply *reply = m_network->post(request, QByteArray(header.toUtf8()));

		QEventLoop loop;
		connect(m_network, SIGNAL(finished(QNetworkReply *)), &loop,
			SLOT(quit()));
		loop.exec();

		QString aQString = QString::fromUtf8(reply->readAll().data());
		std::string json = aQString.toStdString();
		while (json.find("-0001-11-30T00:00:00+0009") != std::string::npos)
			replace(json, "-0001-11-30T00:00:00+0009", "2014-10-18T14:24:39+0200");
		disconnect(m_network);
		myparse.parse(json);
	}
	catch (const std::exception & e) {
		printf(e.what());
	}
}

void Synchro::FirstSynchro()
{
	GetJSON();
	std::map<int, Folder *> FolderMap = myparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;
	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		Folder *fold = iterator->second;
		std::string str = this->folder + fold->getChosenPath() + "/" + fold->getName();
		char *cstr = new char[str.length() + 1];
		strcpy(cstr, str.c_str());
		if (QDir(cstr).exists() == true)
			return;
		else
			QDir().mkdir(cstr);
		delete[] cstr;
	}

	std::map<int, File *> fileMap = myparse.getFileMap();
	std::string StrignUpload = "uploads/";
	typedef std::map<int, File *>::iterator it_type;
	for (it_type iterator = fileMap.begin(); iterator != fileMap.end(); iterator++) {
		File *test = iterator->second;
		std::string urltodown = this->URL + StrignUpload + test->getPseudoOwner() + "/" + test->getRealPath() + test->getPath();
		while (urltodown.find('\\') != std::string::npos)
			replace(urltodown, "\\", "/");
		char *cstr1 = new char[urltodown.length() + 1];
		strcpy(cstr1, urltodown.c_str());

		std::string str = test->getName() + "." + test->getMimeType();
		char *cstr = new char[str.length() + 1];
		strcpy(cstr, str.c_str());

		str = this->folder + test->getChosenPath() + "/";
		char *pathDisk = new char[str.length() + 1];
		strcpy(pathDisk, str.c_str());

		std::string ty = pathDisk + str;
		QString URI(ty.c_str());
		DownloadFile(cstr1, URI);

		delete[] cstr;
		delete[] cstr1;
	}

	char acsjson[1024];
	strcpy(acsjson, this->folder);
	strcat(acsjson, ".acsilserver");
	QFile txt(acsjson);

	txt.open(QIODevice::ReadWrite | QIODevice::Text);
	txt.write(myparse.getJSON().c_str());
	txt.close();
}

void Synchro::ComparatifRenameFodler(QDateTime delta)
{
	qDebug() << delta.toString();
	std::map<int, Folder *> FolderMap = myparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;

	std::map<int, Folder *> OldFolderMap = oldparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type;

	QStringList ListNew, ListOld;

	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {

		Folder *fold = iterator->second;
		ListNew.push_back((this->folder + fold->getChosenPath() + fold->getName()).c_str());
		QString d = QString::fromStdString(fold->getLastModifDate());
		long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(delta);
		if (r < 0)
		{
			std::string tmppath = this->folder + fold->getChosenPath() + fold->getName();
			QString path(tmppath.c_str());

			qDebug() << "Directory updated: " << path;

			for (it_type iterator2 = OldFolderMap.begin(); iterator2 != OldFolderMap.end(); iterator2++) {
				Folder *oldfold = iterator2->second;

				if (oldfold->getId() == fold->getId())
				{
					std::string oldpath = this->folder + oldfold->getChosenPath() + oldfold->getName();
					qDebug() << "Directory Old: " << oldpath.c_str();
					if ((oldfold->getName() != fold->getName()) || (oldpath != path.toStdString()))
					{
						QString original = (this->folder + oldfold->getChosenPath() + oldfold->getName()).c_str();
						QString dest = (this->folder + fold->getChosenPath() + fold->getName()).c_str();
						QDir dir;
						if (!dir.rename(original, dest)){
							qDebug() << "OOOOOOO SECOURSSSSSSSS";

						}
					}
				}
			}
		}
		else
		{

		}
	}
	for (it_type iterator2 = OldFolderMap.begin(); iterator2 != OldFolderMap.end(); iterator2++) {
		Folder *oldfold = iterator2->second;
		ListOld.push_back((this->folder + oldfold->getChosenPath() + oldfold->getName()).c_str());
	}

	QStringList currEntryList = ListNew;

	QStringList newEntryList = ListOld;
	QSet<QString> newDirSet = QSet<QString>::fromList(newEntryList);
	QSet<QString> currentDirSet = QSet<QString>::fromList(currEntryList);
	// Files that have been added
	QSet<QString> newFiles = newDirSet - currentDirSet;
	QStringList newFile = newFiles.toList();
	// Files that have been removed
	QSet<QString> deletedFiles = currentDirSet - newDirSet;
	QStringList deleteFile = deletedFiles.toList();
	if (!deleteFile.isEmpty())
	{
		qDebug() << "Files/Dirs deleted: " << deleteFile;
		foreach(QString file, deleteFile)
		{
			if (QDir(file).exists() == true)
				return;
			else
				QDir().mkdir(file);
		}
	}
	if (!newFile.isEmpty())
	{
		qDebug() << "Files/Dirs deleted: " << newFile;
		foreach(QString file, newFile)
		{
			QDir dir(file);
			dir.removeRecursively();

		}
	}
	//GetJSON();
}

void Synchro::ComparatifCountFodler(QDateTime delta)
{
	//qDebug() << delta.toString();
	std::map<int, File *> FolderMap = myparse.getFileMap();
	typedef std::map<int, File *>::iterator it_type2;

	std::map<int, File *> OldFolderMap = oldparse.getFileMap();
	typedef std::map<int, File *>::iterator it_type;

	QStringList ListNew, ListOld;

	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		File *fold = iterator->second;
		ListNew.push_back((this->folder + fold->getChosenPath() + fold->getName() + "." + fold->getMimeType()).c_str());
		QString d = QString::fromStdString(fold->getLastModifDate());
		long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(delta);
		if (r < 0)
		{
			std::string tmppath = this->folder + fold->getChosenPath() + fold->getName();
			QString path(tmppath.c_str());

			//qDebug() << "Directory updated: " << path;

			for (it_type iterator2 = OldFolderMap.begin(); iterator2 != OldFolderMap.end(); iterator2++) {
				File *oldfold = iterator2->second;

				if (oldfold->getId() == fold->getId())
				{
					std::string oldpath = this->folder + oldfold->getChosenPath() + oldfold->getName();
					//qDebug() << "Directory Old: " << oldpath.c_str();
					if ((oldfold->getName() != fold->getName()) || (oldpath != path.toStdString()))
					{
						QString original = (this->folder + oldfold->getChosenPath() + oldfold->getName()).c_str();
						QString dest = (this->folder + fold->getChosenPath() + fold->getName()).c_str();
						QDir dir;
						if (!dir.rename(original, dest)){
							//qDebug() << "OOOOOOO SECOURSSSSSSSS";

						}
					}
				}
			}
		}
		else
		{

		}
	}
	for (it_type iterator2 = OldFolderMap.begin(); iterator2 != OldFolderMap.end(); iterator2++) {
		File *oldfold = iterator2->second;
		ListOld.push_back((this->folder + oldfold->getChosenPath() + oldfold->getName() + "." + oldfold->getMimeType()).c_str());
	}

	QStringList currEntryList = ListNew;

	QStringList newEntryList = ListOld;
	QSet<QString> newDirSet = QSet<QString>::fromList(newEntryList);
	QSet<QString> currentDirSet = QSet<QString>::fromList(currEntryList);
	// Files that have been added
	QSet<QString> newFiles = newDirSet - currentDirSet;
	QStringList newFile = newFiles.toList();
	// Files that have been removed
	QSet<QString> deletedFiles = currentDirSet - newDirSet;
	QStringList deleteFile = deletedFiles.toList();
	if (!deleteFile.isEmpty())
	{
		//qDebug() << "Files/Dirs doit etre downloader: " << deleteFile;
		foreach(QString file, deleteFile)
		{
			for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++)
			{
				File *test = iterator->second;
				if ((this->folder + test->getChosenPath() + test->getName() + "." + test->getMimeType()).c_str() == file)
				{
					std::string StrignUpload = "uploads/";
					std::string urltodown = this->URL + StrignUpload + test->getPseudoOwner() + "/" + test->getRealPath() + test->getPath();
					while (urltodown.find('\\') != std::string::npos)
						replace(urltodown, "\\", "/");
					char *cstr1 = new char[urltodown.length() + 1];
					strcpy(cstr1, urltodown.c_str());

					std::string str = test->getName() + "." + test->getMimeType();
					char *cstr = new char[str.length() + 1];
					strcpy(cstr, str.c_str());
					DownloadFile(cstr1, file);
				}
			}
		}
	}
	if (!newFile.isEmpty())
	{
		//qDebug() << "Files/Dirs deleted: " << newFile;
		foreach(QString file, newFile)
		{
			QFile fl(file);
			fl.remove();
		}
	}
	GetJSON();
}

void Synchro::Sync(std::string &delta)
{
	this->delta = delta;
	GetJSON();

	addWatchPath(this->folder);
	QStringList Allfileanddir;
	std::map<int, Folder *> FolderMap = myparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;
	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		Folder *fold = iterator->second;
		std::string str = this->folder + fold->getChosenPath() + "/" + fold->getName();
		while (str.find('\\') != std::string::npos)
			replace(str, "\\", "/");
		addWatchPath(str.c_str());
	}
	ComparatifRenameFodler(QDateTime::fromString(delta.c_str(), "yyyy:MM:dd hh:mm:ss"));
	ComparatifCountFodler(QDateTime::fromString(delta.c_str(), "yyyy:MM:dd hh:mm:ss"));
}


void Synchro::DownloadFile(char *url, QString URI)
{
	QNetworkAccessManager *m_network2 = new QNetworkAccessManager();
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	QNetworkReply *reply = m_network2->get(request);
	QEventLoop loop;
	connect(m_network2, SIGNAL(finished(QNetworkReply *)), &loop,
		SLOT(quit()));
	loop.exec();
	disconnect(m_network2);
	QFile txt(URI);
	txt.open(QIODevice::ReadWrite);
	txt.write(reply->readAll());
	txt.close();
}

void Synchro::RenameUp(int id, QString name, type Type)
{

	QStringList list = QString(name).split(".");
	list.removeLast();
	QString str2;

	char toto[1024];
	strcpy(toto, this->URL);
	if (Type == FOLDER)
	{
		str2 = name;
		strcat(toto, "app_dev.php/service/1/op/folderRename");
	}
	else
	{

		str2 = list.join(QString(""));
		strcat(toto, "app_dev.php/service/1/op/rename");
	}

	QString boundary;
	boundary = "-----------------------------7d935033608e2";



	QByteArray datatosend;
	// file
	QByteArray data(QString("--" + boundary + "\r\n").toUtf8());
	data += "Content-Disposition: form-data; name=\"rename[fromId]\"\r\n\r\n";
	data += QString::number(id); // put password if needed
	data += "\r\n";

	data += QString("--" + boundary + "\r\n").toUtf8();
	data += "Content-Disposition: form-data; name=\"rename[toName]\"\r\n\r\n";
	data += QString(str2);
	data += "\r\n";
	data += QString("--" + boundary + "--\r\n").toUtf8();

	m_network = new QNetworkAccessManager();
	QNetworkRequest request;
	QString header = "";

	request.setRawHeader("Content-Type", "multipart/form-data; boundary=-----------------------------7d935033608e2");
	request.setRawHeader("Accept", "application/json, text/plain, */*");

	request.setHeader(QNetworkRequest::ContentLengthHeader, data.size());
	request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));


	request.setUrl(QUrl(toto));

	QEventLoop loop;
	connect(m_network, SIGNAL(finished(QNetworkReply*)), &loop, SLOT(quit()));
	QNetworkReply *reply = m_network->post(request, data);
	loop.exec();

	QString aQString = QString::fromUtf8(reply->readAll().data());
}
void Synchro::DeleteUp(int id, type Type)
{
	char toto[1024];
	strcpy(toto, this->URL);
	QByteArray data;
	QString boundary;
	boundary = "-----------------------------7d935033608e2";
	if (Type == FOLDER)
	{
		strcat(toto, "app_dev.php/service/1/op/folderDelete/");
		strcat(toto, QString::number(id).toStdString().c_str());
		data = "";
	}
	else {

		strcat(toto, "app_dev.php/service/1/op/delete");
		data = QString("--" + boundary + "\r\n").toUtf8();
		data += "Content-Disposition: form-data; name=\"delete[deleteId]\"\r\n\r\n";
		data += QString::number(id);
		data += "\r\n";
	}
	m_network = new QNetworkAccessManager();
	QNetworkRequest request;
	QString header = "";

	request.setRawHeader("Content-Type", "multipart/form-data; boundary=-----------------------------7d935033608e2");
	request.setRawHeader("Accept", "application/json, text/plain, */*");

	request.setHeader(QNetworkRequest::ContentLengthHeader, data.size());
	request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));


	request.setUrl(QUrl(toto));

	QEventLoop loop;
	connect(m_network, SIGNAL(finished(QNetworkReply*)), &loop, SLOT(quit()));
	QNetworkReply *reply = m_network->post(request, data);
	loop.exec();

	QString aQString = QString::fromUtf8(reply->readAll().data());
}
void Synchro::UploadUp(int id, QString filepath, QString filename)
{
	std::string Foldname(filepath.toStdString());
	while (Foldname.find("///") != std::string::npos)
		replace(Foldname, "///", "//");
	while (Foldname.find("//") != std::string::npos)
		replace(Foldname, "//", "/");
	QFileInfo tg(Foldname.c_str());
	QFile file(Foldname.c_str());
	file.open(QIODevice::ReadOnly);
	bool tr = file.exists();
	bool ty = file.isOpen();
	bool ti = file.isReadable();
	char toto[1024];
	strcpy(toto, this->URL);
	strcat(toto, "app_dev.php/service/1/op/upload/");
	strcat(toto, QString::number(id).toStdString().c_str());

	QString name = filename;

	QMimeDatabase mimeDatabase;
	QMimeType mimeType;
	mimeType = mimeDatabase.mimeTypeForFile(file);
	QString MimeType = mimeType.name();
	QString boundary;
	boundary = "-----------------------------7d935033608e2";

	QByteArray datatosend;
	// file
	QByteArray data(QString("--" + boundary + "\r\n").toUtf8());
	data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[file]\";\r\n";
	data += "Content-Type: " + QString(mimeType.name()).toUtf8() + "\r\n\r\n";
	data += file.readAll();
	data += "\r\n";

	// password
	data += QString("--" + boundary + "\r\n").toUtf8();
	data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[name]\"\r\n\r\n";
	data += name; // put password if needed
	data += "\r\n";

	data += QString("--" + boundary + "\r\n").toUtf8();
	data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[Content-Type]\"\r\n\r\n";
	data += mimeType.name();
	data += "\r\n";

	data += QString("--" + boundary + "\r\n").toUtf8();
	data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[Size]\"\r\n\r\n";
	data += QString::number(file.size());
	data += "\r\n";

	data += QString("--" + boundary + "--\r\n").toUtf8();

	file.close();
	m_network = new QNetworkAccessManager();
	QNetworkRequest request;
	QString header = "";

	request.setRawHeader("Content-Type", "multipart/form-data; boundary=-----------------------------7d935033608e2");
	request.setRawHeader("Accept", "application/json, text/plain, */*");

	request.setHeader(QNetworkRequest::ContentLengthHeader, data.size());
	request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));


	request.setUrl(QUrl(toto));

	QEventLoop loop;
	connect(m_network, SIGNAL(finished(QNetworkReply*)), &loop, SLOT(quit()));
	QNetworkReply *reply = m_network->post(request, data);
	loop.exec();

	QString aQString = QString::fromUtf8(reply->readAll().data());
}
void Synchro::NewDirUp(int id, QString name)
{
	char toto[1024];
	strcpy(toto, this->URL);
	strcat(toto, "app_dev.php/service/1/op/folder/");
	strcat(toto, QString::number(id).toStdString().c_str());

	QString boundary;
	boundary = "-----------------------------7d935033608e2";

	QByteArray datatosend;
	// file
	QByteArray data(QString("--" + boundary + "\r\n").toUtf8());
	data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_foldertype[name]\"\r\n\r\n";
	data += name; // put password if needed
	data += "\r\n";
	data += QString("--" + boundary + "--\r\n").toUtf8();
	m_network = new QNetworkAccessManager();
	QNetworkRequest request;
	QString header = "";

	request.setRawHeader("Content-Type", "multipart/form-data; boundary=-----------------------------7d935033608e2");
	request.setRawHeader("Accept", "application/json, text/plain, */*");

	request.setHeader(QNetworkRequest::ContentLengthHeader, data.size());
	request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));


	request.setUrl(QUrl(toto));

	QEventLoop loop;
	connect(m_network, SIGNAL(finished(QNetworkReply*)), &loop, SLOT(quit()));
	QNetworkReply *reply = m_network->post(request, data);
	loop.exec();

	QString aQString = QString::fromUtf8(reply->readAll().data());
	GetJSON();
}
int Synchro::findIdbyName(QString name)
{
	this->Type = FILE;
	std::string Foldname(name.toStdString());
	while (Foldname.find("///") != std::string::npos)
		replace(Foldname, "///", "//");
	while (Foldname.find("//") != std::string::npos)
		replace(Foldname, "//", "/");
	std::map<int, File *> fileMap = myparse.getFileMap();
	typedef std::map<int, File *>::iterator it_type;
	for (it_type iterator = fileMap.begin(); iterator != fileMap.end(); iterator++) {
		File *test = iterator->second;
		int id = test->getId();
		std::string ert = this->folder + test->getChosenPath() + test->getName() + "." + test->getMimeType();
		while (ert.find("\\") != std::string::npos)
			replace(ert, "\\", "/");
		while (ert.find("///") != std::string::npos)
			replace(ert, "///", "//");


		while (ert.find("//") != std::string::npos)
			replace(ert, "//", "/");
		if (ert == Foldname)
			return test->getId();
	}

	this->Type = FOLDER;
	if (name == this->folder)
		return 0;
	std::map<int, Folder *> FolderMap = myparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;
	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		Folder *fold = iterator->second;
		std::string a = this->folder + fold->getChosenPath() + fold->getName();
		while (a.find('\\') != std::string::npos)
			replace(a, "\\", "/");
		while (Foldname.find("///") != std::string::npos)
			replace(Foldname, "///", "//");
		while (Foldname.find("//") != std::string::npos)
			replace(Foldname, "//", "/");
		while (a.find("//") != std::string::npos)
			replace(a, "//", "/");
		if (a == Foldname)
			return fold->getId();
	}
	this->Type = UNKNOWN;
	return -1;
}

void Synchro::addWatchPath(QString path)
{
	//qDebug() << "Add to watch: " << path;
	_instance->_sysWatcher->addPath(path);  //add path to watch
	// Save the list of current contents if the added path is a directory

	QFileInfo f(path);

	if (f.isDir())
	{
		const QDir dirw(path);
		_instance->_currContents[path] = dirw.entryList(QDir::NoDotAndDotDot | QDir::AllDirs | QDir::Files, QDir::DirsFirst);
	}

}
void Synchro::SuppWatchPath(QString path)
{
	//qDebug() << "Supp to watch: " << path;
	_instance->_sysWatcher->removePath(path);  //add path to watch
	// Save the list of current contents if the added path is a directory

	QFileInfo f(path);

	if (f.isDir())
	{
		const QDir dirw(path);

		_instance->_currContents[path].detach();// = dirw.entryList(QDir::NoDotAndDotDot | QDir::AllDirs | QDir::Files, QDir::DirsFirst);
	}

}
void Synchro::directoryUpdated(const QString & path)
{
	//qDebug() << "Directory updated: " << path;

	// Compare the latest contents to saved contents for the dir updated to find out the difference(change) 
	QStringList currEntryList = _instance->_currContents[path];
	const QDir dir(path);
	QStringList newEntryList = dir.entryList(QDir::NoDotAndDotDot | QDir::AllDirs | QDir::Files, QDir::DirsFirst);
	QSet<QString> newDirSet = QSet<QString>::fromList(newEntryList);
	QSet<QString> currentDirSet = QSet<QString>::fromList(currEntryList);
	// Files that have been added
	QSet<QString> newFiles = newDirSet - currentDirSet;
	QStringList newFile = newFiles.toList();
	// Files that have been removed
	QSet<QString> deletedFiles = currentDirSet - newDirSet;
	QStringList deleteFile = deletedFiles.toList();
	// Update the current set
	_instance->_currContents[path] = newEntryList;

	if (!newFile.isEmpty() && !deleteFile.isEmpty())
	{
		// File/Dir is renamed

		if (newFile.count() == 1 && deleteFile.count() == 1)
		{
			QString fold = path + "/" + deleteFile.first();
			int id = findIdbyName(fold);
			///faire que si dir
			addWatchPath(path + "/" + newFile.first());
			SuppWatchPath(fold);
			if (id != -1)
				RenameUp(id, newFile.first(), this->Type);
			//qDebug() << "File Renamed from " << newFile.first() << " to " << deleteFile.first();

			GetJSON();
		}
	}

	else
	{
		// New File/Dir Added to Dir
		if (!newFile.isEmpty())
		{

			//qDebug() << "New Files/Dirs added: " << newFile;

			foreach(QString file, newFile)
			{
				QString fold(path);
				QString target = path + "/" + file;
				QFileInfo InfoTarget(target);
				/// faire que si dir
				addWatchPath(target);
				int id = findIdbyName(fold);
				if (id != -1) {
					if (InfoTarget.isDir() == true)
						NewDirUp(id, file);
					else
						UploadUp(id, fold + "/" + file, file);
				}
			}
			GetJSON();
		}

		// File/Dir is deleted from Dir

		if (!deleteFile.isEmpty())
		{
			//qDebug() << "Files/Dirs deleted: " << deleteFile;
			foreach(QString file, deleteFile)
			{
				QString fold(path);
				QString target = path + "/" + file;
				//SuppWatchPath(target);
				int id = findIdbyName(target);
				if (id != -1)
					DeleteUp(id, this->Type);
			}
			GetJSON();
		}

	}

}
void Synchro::fileUpdated(const QString & path)
{
	QFileInfo file(path);

	QString path1 = file.absolutePath();

	QString name = file.fileName();
	///delete du fichier
	//uplaod du fichier
	qDebug() << "The file " << name << " at path " << path1 << " is updated";
}
