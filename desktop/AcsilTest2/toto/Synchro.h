
#ifndef SYNCHRO_H
#define  SYNCHRO_H

#include "Parser.h"
#include <QtCore/QUrl>
#include <qeventloop.h>
#include <QUrl>
#include <QtNetwork/QNetworkAccessManager>
#include <QtNetwork/QNetworkRequest>
#include <QtNetwork/QNetworkReply>
#include <QtNetwork/QNetworkProxy>
#include <QtXml/QDomDocument>
#include <QObject>
#include <qfile.h>
#include <exception>
#include <QJsonDocument>
#include <QJsonObject>
#include <qjsonarray.h>
#include <QDir>
#include <QScriptValue>
#include <qscriptvalueiterator.h>
#include <qscriptengine.h>
#include <QDirIterator>
#include <QMimeDatabase>
#include <QMimeType>
#include <QTimer>
#include <iostream>
#include <map>
#include <fstream>
#include <string>
#include <ctime>
#include <QFileSystemWatcher>

class Synchro : public QObject
{
	Q_OBJECT

private:
	enum type {
		FILE,
		FOLDER,
		UNKNOWN
	};
	const char *xtoken;
	char *folder;
	char *URL;
	std::string delta;
	void GetJSON();
	QNetworkAccessManager *m_network;
	void DownloadFile(char *url, QString name);
	void ComparatifRenameFodler(QDateTime delta);
	void ComparatifCountFodler(QDateTime delta);
	Parser myparse;
	Parser oldparse;
	int findIdbyName(QString );
	type Type;
public:
	static Synchro* _instance;
	QMap<QString, QStringList> _currContents; //maintain list of current contents of each watched directory
	QFileSystemWatcher* _sysWatcher;  //QFileSystemWatcher variable
	static void addWatchPath(QString path);
	static void SuppWatchPath(QString path);
	void FirstSynchro();
	Synchro(const char *token, char *folder, char *URL);
	~Synchro();
	void Sync(std::string &delta);
	void RenameUp(int id, QString name, type Type);
	void DeleteUp(int id, type Type);
	void UploadUp(int id, QString filepath, QString filename);
	void NewDirUp(int id, QString name);

	public  slots:
	void directoryUpdated(const QString & path);  
	void fileUpdated(const QString & path);
	

};

#endif