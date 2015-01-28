#ifndef AUTHENTIFICATION_H
#define AUTHENTIFICATION_H

#include <QtNetwork/QNetworkAccessManager>
#include <QtNetwork/QNetworkRequest>
#include <QtNetwork/QNetworkReply>
#include <QtXml/QDomDocument>
#include <QJsonDocument>
#include <QJsonObject>


class Authentification : public QObject
{
	Q_OBJECT

public:
	const char *getToken()
	{
		if (token != "")
			return this->token.c_str();
		else
			return "";
	}
	Authentification(char *, char *USER, char *PASSWORD);
	~Authentification();

private:
	std::string token;
	QNetworkAccessManager *m_network;

};

#endif // AUTHENTIFICATION_H
