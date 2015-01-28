#include "Authentification.h"
#include <qeventloop.h>

static const char *grantType = "password";
static const char *applicationID = "1_powyjhqgq28scskw0w04wg8wck8osksgko0ggwgk44kokwo8k";
static const char *clientString = "29zjq3ov25hccgk48k84swwo800gccoo08wk40sw48s00gc8kw";


Authentification::Authentification(char *URL, char *USER, char *PASSWORD)
{
	this->token = "";
	
	char REQUEST_URL[1024];
	strcpy(REQUEST_URL, URL);
	strcat(REQUEST_URL, "app_dev.php/oauth/v2/token");

	m_network = new QNetworkAccessManager();
	QNetworkRequest request;
	QString header = "grant_type=";
	header += grantType;
	header += "&client_id=";
	header += applicationID;
	header += "&client_secret=";
	header += clientString;
	header += "&username=";
	header += USER;
	header += "&password=";
	header += PASSWORD;


	request.setRawHeader("Content-Type", "application/x-www-form-urlencoded");
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");


	request.setUrl(QUrl(REQUEST_URL));
	QNetworkReply *reply = m_network->post(request, QByteArray(header.toUtf8()));

	QEventLoop loop;
	connect(m_network, SIGNAL(finished(QNetworkReply *)), &loop,
		SLOT(quit()));
	loop.exec();

	QJsonDocument d = QJsonDocument::fromJson(reply->readAll());
	QJsonObject sett2 = d.object();
	QString value = sett2["access_token"].toString();
	std::string utf8_text = value.toUtf8().constData();
	this->token = utf8_text;
	disconnect(m_network);
}

Authentification::~Authentification()
{

}

