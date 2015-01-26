
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
	const char *xtoken;
	char *folder;
	char *URL;
	std::string delta;
	void GetJSON();
	QNetworkAccessManager *m_network;
	void CreateFodler(char *path);
	void DownloadFile(char *url, char *name, char *path);
	bool DownSync(char *pathDisk, char *idfolderweb, std::string &delta);
	void UpSync(char *folder, std::string &result);
	void ComparatifCountFodler();
	Parser myparse;
	Parser oldparse;
	int findIdbyName(QString &);
public:
	static Synchro* _instance;
	QMap<QString, QStringList> _currContents; //maintain list of current contents of each watched directory
	QFileSystemWatcher* _sysWatcher;  //QFileSystemWatcher variable
	static void addWatchPath(QString path);

	void Test();
	void FirstSynchro();
	Synchro(const char *token, char *folder, char *URL);
	~Synchro();
	void Sync(std::string &delta);
	void RenameUp(int id, QString name);
	void DeleteUp(int id);
	void UploadUp(int id, QString filepath, QString filename);

	public  slots:
	void directoryUpdated(const QString & path);  
	void fileUpdated(const QString & path);
	

};

#endif