#include "Synchro.h"
Synchro* Synchro::_instance = 0;

Synchro::Synchro(const char *token, char *folder, char *URL)
	: folder(folder), URL(URL)
{
	_instance = this;
		/*_instance = new Synchro(token, folder, URL);*/
		this->_sysWatcher = new QFileSystemWatcher();

		// Connect the directoryChanged and fileChanged signals of QFileSystemWatcher to corresponding slots

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

		char acsjson[1024];
		strcpy(acsjson, this->folder);
		strcat(acsjson, ".acsilserver");
		QFile txt(acsjson);
		txt.open(QIODevice::ReadWrite | QIODevice::Text);
		txt.write(myparse.getJSON().c_str());
		txt.close();
		oldparse.parse(myparse.getJSON());
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
		CreateFodler(cstr);
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
		DownloadFile(cstr1, cstr, pathDisk);

		delete[] cstr;
		delete[] cstr1;
	}
}

void Synchro::ComparatifCountFodler()
{
/*	std::map<int, Folder *> FolderMap = myparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;
	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		Folder *fold = iterator->second;

		QString d = QString::fromStdString(this->delta);
		QString d2 = QString::fromStdString(fold->getLastModifDate());
		long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(QDateTime::fromString(d2, "yyyy:MM:dd hh:mm:ss"));
		if (r >= 0)
		{
			int id = fold->getId();

			std::map<int, Folder *> OldFolderMap = oldparse.getFoldermap();
			typedef std::map<int, Folder *>::iterator it_type;
			for (it_type iterator = OldFolderMap.begin(); iterator != OldFolderMap.end(); iterator++) {
				Folder *oldfold = iterator->second;

				if (id == oldfold->getId())
				{
					std::string strold = this->folder + oldfold->getChosenPath() + "/" + oldfold->getName();
					char *cstrold = new char[strold.length() + 1];
					strcpy(cstrold, strold.c_str());
					int dif = fold->getSize() - QDir(cstrold).count;
					if (dif < 0)
					{

					}
					else if (dif > 0)
					{

					}

					QFileInfo file(cstrold);
					QDateTime lastModified = file.lastModified();

					std::string str = this->folder + fold->getChosenPath() + "/" + fold->getName();
					char *cstr = new char[str.length() + 1];
					strcpy(cstr, str.c_str());

					if (cstr != cstrold)
					{
						//faire le deplacement
					}


					if (oldfold->getName() != fold->getName())
					{

					}
					
				}

			}
			///tu fais le schema de count,de modification du nom, etc
		}



		std::string str = this->folder + fold->getChosenPath() + "/" + fold->getName();
		char *cstr = new char[str.length() + 1];
		strcpy(cstr, str.c_str());

		/*QString d = QString::fromStdString(delta);
		QString d2 = QString::fromStdString(fold->getLastModifDate());
		long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(QDateTime::fromString(d2, "yyyy:MM:dd hh:mm:ss"));
		if (r >= 0)	{
		std::string str = pathDisk + fold->getName() + "/";
		char *cstr = new char[str.length() + 1];
		strcpy(cstr, str.c_str());
		CreateFodler(cstr);
		int id = fold->getId();
		char buf[10];
		sprintf(buf, "%d", id);
		DownSync(cstr, buf, delta);
		delete[] cstr;
		}*/
	//}


}


void Synchro::Sync(std::string &delta)
{
	addWatchPath("D:/acsil");

	this->delta = delta;
	///Faire un diff entre la derniere date de modification depuis l'api et le lastupadtetime du dossier racine local
	GetJSON();
	

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
	
	//ComparatifCountFodler();

	
	//DownSync(this->folder, "0", delta);
	//UpSync(this->folder, delta);
}

bool Synchro::DownSync(char *pathDisk, char *idfolderweb, std::string &delta)
{
	char toto[1024];
	strcpy(toto, this->URL);
	strcat(toto, "app_dev.php/service/1/op/list/");
	strcat(toto, idfolderweb);
	QDateTime a;
	QString d = QString::fromStdString(delta);
	a.fromString(d, "yyyy:MM:dd hh:mm:ss");


	try {
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

			QString d = QString::fromStdString(delta);
			QString d2 = QString::fromStdString(test->getUploadDate());
			long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").daysTo(QDateTime::fromString(d2, "yyyy:MM:dd hh:mm:ss"));
			if (r >= 0)
				DownloadFile(cstr1, cstr, pathDisk);

			delete[] cstr;
			delete[] cstr1;
		}

		std::map<int, Folder *> FolderMap = myparse.getFoldermap();
		typedef std::map<int, Folder *>::iterator it_type2;
		for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
			Folder *fold = iterator->second;

			QString d = QString::fromStdString(delta);
			QString d2 = QString::fromStdString(fold->getLastModifDate());
			long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(QDateTime::fromString(d2, "yyyy:MM:dd hh:mm:ss"));
			if (r >= 0)	{
				std::string str = pathDisk + fold->getName() + "/";
				char *cstr = new char[str.length() + 1];
				strcpy(cstr, str.c_str());
				CreateFodler(cstr);
				int id = fold->getId();
				char buf[10];
				sprintf(buf, "%d", id);
				DownSync(cstr, buf, delta);
				delete[] cstr;
			}
		}
	}
	catch (const std::exception & e) {
		printf(e.what());
	}
	return true;
}

void Synchro::CreateFodler(char *path)
{

	if (QDir(path).exists() == true)
		return;
	else
		QDir().mkdir(path);
}

void Synchro::DownloadFile(char *url, char *name, char *path)
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
	int a = strlen(path) + strlen(name);
	char *URI = (char*)malloc(a);
	strcpy(URI, path);
	strcat(URI, name);
	QFile txt(URI);
	txt.open(QIODevice::ReadWrite);
	txt.write(reply->readAll());
	txt.close();
}

void Synchro::UpSync(char *folder, std::string &delta)
{
	QDateTime a;
	QString d = QString::fromStdString(delta);
	a.fromString(d, "yyyy:MM:dd hh:mm:ss");
	QDir dir(folder);
	dir.setFilter(QDir::NoDotAndDotDot | QDir::AllEntries);
	QFileInfoList list = dir.entryInfoList();
	for (int i = 0; i < list.size(); ++i) {
		QFileInfo fichier = list.at(i);
		QString name = fichier.fileName();
		QString test = fichier.created().toString("yyyy:MM:dd hh:mm:ss");
		long long r = QDateTime::fromString(d, "yyyy:MM:dd hh:mm:ss").msecsTo(QDateTime::fromString(test, "yyyy:MM:dd hh:mm:ss"));
		if (r >= 0) {
			if (!fichier.isDir()) {
				QFile file(fichier.absoluteFilePath());
				file.open(QIODevice::ReadOnly);
				char toto[1024];
				strcpy(toto, this->URL);
				strcat(toto, "app_dev.php/service/1/op/upload/");

				strcat(toto, "9");

				QMimeDatabase mimeDatabase;
				QMimeType mimeType;

				mimeType = mimeDatabase.mimeTypeForFile(fichier);
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

				std::string ttyu = "dzfsed";
			}
			else if (fichier.absoluteFilePath().toStdString() != folder) {
				std::string str = fichier.absoluteFilePath().toStdString();
				char *cstr = new char[str.length() + 1];
				strcpy(cstr, str.c_str());
				UpSync(cstr, delta);
				delete[] cstr;
			}
		}
	}
}


void Synchro::RenameUp(int id, QString name)
{

	QStringList list = QString(name).split(".");
	list.removeLast();
	QString str2 = list.join(QString(""));

	char toto[1024];
	strcpy(toto, this->URL);
	strcat(toto, "app_dev.php/service/1/op/rename");

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
void Synchro::DeleteUp(int id)
{
	char toto[1024];
	strcpy(toto, this->URL);
	strcat(toto, "app_dev.php/service/1/op/delete");

	QString boundary;
	boundary = "-----------------------------7d935033608e2";



	QByteArray datatosend;
	// file
	QByteArray data(QString("--" + boundary + "\r\n").toUtf8());
	data += "Content-Disposition: form-data; name=\"delete[deleteId]\"\r\n\r\n";
	data += QString::number(id);
	data += "\r\n";

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
int Synchro::findIdbyName(QString &name)
{
	std::string Foldname(name.toStdString());
	while (Foldname.find("///") != std::string::npos)
		replace(Foldname, "///", "//");
	std::map<int, File *> fileMap = oldparse.getFileMap();
	typedef std::map<int, File *>::iterator it_type;
	for (it_type iterator = fileMap.begin(); iterator != fileMap.end(); iterator++) {
		File *test = iterator->second;
		int id = test->getId();
		if ((test->getName() + "." + test->getMimeType()) == Foldname)
			return test->getId();
	}

	std::map<int, Folder *> FolderMap = oldparse.getFoldermap();
	typedef std::map<int, Folder *>::iterator it_type2;
	for (it_type2 iterator = FolderMap.begin(); iterator != FolderMap.end(); iterator++) {
		Folder *fold = iterator->second;
		std::string a = this->folder+fold->getChosenPath()+fold->getName();
		while (a.find('\\') != std::string::npos)
			replace(a, "\\", "/");
		if (a == Foldname)
			return fold->getId();
	}
	return -1;
}

void Synchro::addWatchPath(QString path)
{
	qDebug() << "Add to watch: " << path;
	_instance->_sysWatcher->addPath(path);  //add path to watch

	// Save the list of current contents if the added path is a directory

	QFileInfo f(path);

	if (f.isDir())
	{
		const QDir dirw(path);
		_instance->_currContents[path] = dirw.entryList(QDir::NoDotAndDotDot | QDir::AllDirs | QDir::Files, QDir::DirsFirst);
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
			///enlever tout ce qu'il y a apres le '.'
			///faire la diff entre fichier et dossier
			 int id = findIdbyName(deleteFile.first());
			 if (id != -1)
				RenameUp(id, newFile.first());
			//qDebug() << "File Renamed from " << newFile.first() << " to " << deleteFile.first();
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
				int id = findIdbyName(fold);
				if (id != -1)
					UploadUp(id, fold+"/"+file, file);
				//Handle Operation on new files.....
			}
		}

		// File/Dir is deleted from Dir

		if (!deleteFile.isEmpty())
		{
			//qDebug() << "Files/Dirs deleted: " << deleteFile;
			foreach(QString file, deleteFile)
			{
				
				//faire la diff entre fihcier et dossier
				int id = findIdbyName(file);
				if (id != -1)
					DeleteUp(id);
			}
		}

	}
	GetJSON();
}

void Synchro::fileUpdated(const QString & path)
{
	QFileInfo file(path);

	QString path1 = file.absolutePath();

	QString name = file.fileName();

	//qDebug() << "The file " << name << " at path " << path1 << " is updated";
}