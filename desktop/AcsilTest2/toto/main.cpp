
#include <QtCore/QCoreApplication>
#include "Authentification.h"
#include <iostream> 
#include "Synchro.h"
#include <QTimer>
#include <QThread>

int main(int argc, char *argv[]) 
{
	QCoreApplication a(argc, argv);
	/////////// NE PAS OUBLIER DE LOADER LES INFORMATIONS COMME URL ET LOGIN/MDP DANS LA CLASSE OAUTH ///////////////
	Authentification oauth =  Authentification("http://galan.im/", "guillaume.galan@gmail.com", "admin");

	
	
	const char *token = oauth.getToken();
	
	char *fold = "D:/acsil/";
	char *URL = "http://galan.im/";
	Synchro sync = Synchro(token, fold, URL);
	//sync.FirstSynchro();
	sync.Sync((std::string)"2015:01:28 00:52:00");


	return a.exec();
}
