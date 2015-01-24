#include "Synchro.h"


Synchro::Synchro(const char *token, char *folder, char *URL)
	: folder(folder), URL(URL)
{
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

void Synchro::Sync(std::string &delta)
{
	//DownSync(this->folder, "0", delta);
	UpSync(this->folder, delta);
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

		Parser myparse;
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
		if (fichier.lastModified() > a) {
			if (!fichier.isDir()) {
				QFile file(fichier.absoluteFilePath());
				file.open(QIODevice::ReadOnly);
				char toto[1024];
				strcpy(toto, this->URL);
				strcat(toto, "app_dev.php/service/1/op/upload/");
				strcat(toto, "0");

				QMimeDatabase mimeDatabase;
				QMimeType mimeType;

				mimeType = mimeDatabase.mimeTypeForFile(fichier);
				QString MimeType = mimeType.name();
				QString boundary;
				boundary = "-----------------------------7d935033608e2";

				QByteArray datatosend;
				QString data = "--" + boundary + "\r\n";
				data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[file]\";\r\n";
				data += "Content-Type: " + mimeType.name() + "\r\n\r\n";
				data += file.readAll();
				data += "\r\n";


				data += "--" + boundary + "\r\n";
				data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[name]\"\r\n\r\n";
				data += name;
				data += "\r\n";

				data += "--" + boundary + "\r\n";
				data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[Content-Type]\"\r\n\r\n";
				data += mimeType.name();
				data += "\r\n";

				data += "--" + boundary + "--\r\n";
				datatosend = data.toUtf8();


				// file
				/*QByteArray data(QString("--" + boundary + "\r\n").toUtf8());
				data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[file]\";\r\n";
				data += "Content-Type: image/jpeg\r\n\r\n";
				data += file.readAll();
				data += "\r\n";

				// password
				data += QString("--" + boundary + "\r\n").toUtf8();
				data += "Content-Disposition: form-data; name=\"acsilserver_appbundle_documenttype[name]\"\r\n\r\n";
				data += "versailles.jpg\r\n"; // put password if needed
				data += "\r\n";

				data += QString("--" + boundary + "--\r\n").toUtf8();*/




				file.close();
				m_network = new QNetworkAccessManager();
				QNetworkRequest request;
				QString header = "";

				request.setRawHeader("Content-Type", "multipart/form-data; boundary=-----------------------------7d935033608e2");
				request.setRawHeader("Accept", "application/json, text/plain, */*");

				request.setHeader(QNetworkRequest::ContentLengthHeader, datatosend.size());
				request.setRawHeader("Authorization", "Bearer " + QByteArray(this->xtoken));


				request.setUrl(QUrl(toto));

				QEventLoop loop;
				connect(m_network, SIGNAL(finished(QNetworkReply*)), &loop, SLOT(quit()));
				QNetworkReply *reply = m_network->post(request, datatosend);
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