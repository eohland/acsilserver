#include <iostream>
#include <map>
#include <fstream>
#include <string>
#include <ctime>
#include "File.h"
#include "Folder.h"
#include "User.h"
#include <QObject>
#include <QDateTime>

class Parser
{
public:
	Parser();
	~Parser();
	std::map<int, File *>	&getFileMap();
	std::map<int, Folder *> &getFoldermap();
	std::map<int, User *>	&getUserMap();
	void					parse(std::string&);
	std::time_t				strToTime(std::string&);
	void					fillFile(std::string&);
	void					fillFolder(std::string&);
	void					fillUser(std::string&);
	void					findData(std::string&, unsigned int, char);

private:
	std::map<int, File *>	_fileMap;
	std::map<int, Folder *> _folderMap;
	std::map<int, User *>	_userMap;
	std::string				_cutData;
	unsigned int			_pos;
};