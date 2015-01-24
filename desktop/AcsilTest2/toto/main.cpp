
#include <QtCore/QCoreApplication>
#include "Authentification.h"
#include <iostream> 
#include "Synchro.h"

int main(int argc, char *argv[])
{
	QCoreApplication a(argc, argv);
	Authentification oauth =  Authentification();

	
	
	const char *token = oauth.getToken();
	
	char *fold = "D:/acsil/";
	char *URL = "http://galan.im/";
	Synchro sync = Synchro(token, fold, URL);
	sync.Sync((std::string)"2015:01:24 15:34:00");
	


	return a.exec();
}
