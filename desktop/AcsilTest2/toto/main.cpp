
#include <QtCore/QCoreApplication>
#include "Authentification.h"
#include <iostream> 
#include "Synchro.h"
#include <QTimer>
#include <QThread>

int main(int argc, char *argv[]) 
{
	QCoreApplication a(argc, argv);
	Authentification oauth =  Authentification();

	
	
	const char *token = oauth.getToken();
	
	char *fold = "D:/acsil/";
	char *URL = "http://galan.im/";
	Synchro sync = Synchro(token, fold, URL);
	//sync.FirstSynchro();
	sync.Sync((std::string)"2015:01:25 11:34:00");



	/*QTimer *timer = new QTimer();
	timer->connect(timer, SIGNAL(timeout(std::string)), SLOT(sync.Sync((std::string))));
	timer->start(10000);

	*/
	return a.exec();
}
