
#ifndef SYNCHRO_H
#define  SYNCHRO_H

#include "Parser.h"
#include <QtCore/QUrl>
#include <qeventloop.h>
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

class Synchro : public QObject
{
	Q_OBJECT

private:
	const char *xtoken;
	char *folder;
	char *URL;
	void GetJSON(char *URL);
	QNetworkAccessManager *m_network;
	void CreateFodler(char *path);
	void DownloadFile(char *url, char *name, char *path);
	bool DownSync(char *pathDisk, char *idfolderweb, std::string &delta);
	void UpSync(char *folder, std::string &result);

public:
	Synchro(const char *token, char *folder, char *URL);
	~Synchro();
	void Sync(std::string &delta);
	
};

#endif