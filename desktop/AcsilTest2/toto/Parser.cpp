#include "Parser.h"
#include <windows.h>

Parser::Parser()
{
}

Parser::~Parser()
{
}

std::map<int, File *>	&Parser::getFileMap()
{
	return(this->_fileMap);
}

std::map<int, Folder *> &Parser::getFoldermap()
{
	return(this->_folderMap);
}

std::map<int, User *>	&Parser::getUserMap()
{
	return(this->_userMap);
}

void	Parser::parse(std::string &data)
{
	fillFile(data);
	fillFolder(data);
	fillUser(data);
}

std::time_t		Parser::strToTime(std::string& str)
{
	struct tm  timeinfo;

	timeinfo.tm_year = std::stoi(str.substr(0, 4));
	timeinfo.tm_mon = std::stoi(str.substr(5, 2));
	timeinfo.tm_mday = std::stoi(str.substr(8, 2));
	timeinfo.tm_hour = std::stoi(str.substr(11, 2));
	timeinfo.tm_min = std::stoi(str.substr(14, 2));
	timeinfo.tm_sec = std::stoi(str.substr(17, 2));
	timeinfo.tm_isdst = 0; // disable daylight saving time

	std::time_t ret = mktime(&timeinfo);
	return (ret);
}

void				Parser::findData(std::string& data, unsigned int pos, char endChar)
{
	unsigned int endPos;

	endPos = data.find(endChar, pos);//find end of data

	if (endChar == ']')//for usertype and roles because of "
		endPos++;
	this->_cutData = data.substr(pos, endPos - pos);

	this->_pos = endPos;//to find next data
}

void					Parser::fillFile(std::string& data)
{
	this->_pos = 0;
	if (data[10] != ']') {//if no file
		//std::cout << "FILE" << std::endl;
		while (42) {
			File *file = new File();
			this->_pos = data.find("\"id\":", this->_pos);// "id"
			findData(data, this->_pos + 5, ',');
			file->setId(std::stoi(this->_cutData));
			//std::cout << "id = " << file->getId() << std::endl;
			this->_pos = data.find("\"real_path\":\"", this->_pos);// "real_path"
			findData(data, this->_pos + 13, '"');
			file->setRealPath(this->_cutData);
			//std::cout << "real_path = " << file->getRealPath() << std::endl;
			this->_pos = data.find("\"chosen_path\":\"", this->_pos);// "chosen_path"
			findData(data, this->_pos + 15, '"');
			file->setChosenPath(this->_cutData);
			//std::cout << "chosen_path = " << file->getChosenPath() << std::endl;
			this->_pos = data.find("\"size\":", this->_pos);// "size"
			findData(data, this->_pos + 7, ',');
			file->setSize(std::stoi(this->_cutData));
			//std::cout << "size = " << file->getSize() << std::endl;
			this->_pos = data.find("\"name\":\"", this->_pos);// "name"
			findData(data, this->_pos + 8, '"');
			file->setName(this->_cutData);
			//std::cout << "name = " << file->getName() << std::endl;
			this->_pos = data.find("\"path\":\"", this->_pos);// "path"
			findData(data, this->_pos + 8, '"');
			file->setPath(this->_cutData);
			//std::cout << "path = " << file->getPath() << std::endl;
			this->_pos = data.find("\"owner\":\"", this->_pos);// "owner"
			findData(data, this->_pos + 9, '"');
			file->setOwner(this->_cutData);
			//std::cout << "owner = " << file->getOwner() << std::endl;
			this->_pos = data.find("\"pseudo_owner\":\"", this->_pos);// "pseudo_owner"
			findData(data, this->_pos + 16, '"');
			file->setPseudoOwner(this->_cutData);
			//std::cout << "pseudo = " << file->getPseudoOwner() << std::endl;
			this->_pos = data.find("\"upload_date\":\"", this->_pos);// "upload_date"
			findData(data, this->_pos + 15, '"');
			file->setUploadDate(this->_cutData);
			this->_pos = data.find("\"last_modif_date\":\"", this->_pos);// "last_modif_date"
			findData(data, this->_pos + 19, '"');
			file->setLastModifDate(this->_cutData);
			this->_pos = data.find("\"is_profile_picture\":", this->_pos);// "is_profile_picture"
			findData(data, this->_pos + 21, ',');
			file->setIsProfilePicture(std::stoi(this->_cutData));
			//std::cout << "profilepic = " << file->getIsProfilePicture() << std::endl;
			this->_pos = data.find("\"is_shared\":", this->_pos);// "is_shared"
			findData(data, this->_pos + 12, ',');
			file->setIsShared(std::stoi(this->_cutData));
			//std::cout << "isSHared = " << file->getIsShared() << std::endl;
			this->_pos = data.find("\"mime_type\":\"", this->_pos);// "mime_type"
			findData(data, this->_pos + 13, '"');
			file->setMimeType(this->_cutData);
			//std::cout << "mimeType = " << file->getMimeType() << std::endl;
			this->_pos = data.find("\"formated_size\":\"", this->_pos);// "formated_size"
			findData(data, this->_pos + 17, '"');
			file->setFormatedSize(this->_cutData);
			//std::cout << "formatSize = " << file->getFormatedSize() << std::endl;
			this->_pos = data.find("\"folder\":", this->_pos);// "folder"
			findData(data, this->_pos + 9, '}');
			file->setFolder(std::stoi(this->_cutData));
			//std::cout << "folder = " << file->getFolder() << std::endl;
			this->_pos = data.find("\"sharedFileUserInfos\":\"", this->_pos);// "sharedFileUserInfos"
			findData(data, this->_pos + 23, '"');
			file->setSharedFileUserInfos(this->_cutData);
			//std::cout << "sharedFileInfos = " << file->getSharedFileUserInfos() << std::endl;

			this->_fileMap[file->getId()] = file;

			if (data.find("\"info\":", this->_pos) == -1)//Check if end
				return;
		}
	}
}

void	Parser::fillFolder(std::string& data)
{
	unsigned int pos = data.find("\"folders\":[", this->_pos);
	if (data[pos + 11] != ']') {//if no folder
		//std::cout << "FOLDER" << std::endl;
		while (42) {
			Folder *folder = new Folder();
			this->_pos = data.find("\"id\":", this->_pos);// "id"
			findData(data, this->_pos + 5, ',');
			folder->setId(std::stoi(this->_cutData));
			//std::cout << "id = " << folder->getId() << std::endl;
			this->_pos = data.find("\"real_path\":\"", this->_pos);// "real_path"
			findData(data, this->_pos + 13, '"');
			folder->setRealPath(this->_cutData);
			//std::cout << "real_path = " << folder->getRealPath() << std::endl;
			this->_pos = data.find("\"chosen_path\":\"", this->_pos);// "chosen_path"
			findData(data, this->_pos + 15, '"');
			folder->setChosenPath(this->_cutData);
			//std::cout << "chosen_path = " << folder->getChosenPath() << std::endl;
			this->_pos = data.find("\"size\":", this->_pos);// "size"
			findData(data, this->_pos + 7, ',');
			folder->setSize(std::stoi(this->_cutData));
			//std::cout << "size = " << folder->getSize() << std::endl;
			this->_pos = data.find("\"name\":\"", this->_pos);// "name"
			findData(data, this->_pos + 8, '"');
			folder->setName(this->_cutData);
			//std::cout << "name = " << folder->getName() << std::endl;
			this->_pos = data.find("\"path\":\"", this->_pos);// "path"
			findData(data, this->_pos + 8, '"');
			folder->setPath(this->_cutData);
			//std::cout << "path = " << folder->getPath() << std::endl;
			this->_pos = data.find("\"owner\":\"", this->_pos);// "owner"
			findData(data, this->_pos + 9, '"');
			folder->setOwner(this->_cutData);
			//std::cout << "owner = " << folder->getOwner() << std::endl;
			this->_pos = data.find("\"pseudo_owner\":\"", this->_pos);// "pseudo_owner"
			findData(data, this->_pos + 16, '"');
			folder->setPseudoOwner(this->_cutData);
			//std::cout << "pseudo = " << folder->getPseudoOwner() << std::endl;
			this->_pos = data.find("\"upload_date\":\"", this->_pos);// "upload_date"
			findData(data, this->_pos + 15, '"');
			folder->setUploadDate(this->_cutData);
			this->_pos = data.find("\"last_modif_date\":\"", this->_pos);// "last_modif_date"
			findData(data, this->_pos + 19, '"');
			folder->setLastModifDate(this->_cutData);
			this->_pos = data.find("\"parent_folder\":", this->_pos);// "parent_folder"
			findData(data, this->_pos + 16, ',');
			folder->setParentFolder(std::stoi(this->_cutData));
			//std::cout << "parent folder = " << folder->getParentFolder() << std::endl;
			this->_pos = data.find("\"is_shared\":", this->_pos);// "is_shared"
			findData(data, this->_pos + 12, ',');
			folder->setIsShared(std::stoi(this->_cutData));
			//std::cout << "isSHared = " << folder->getIsShared() << std::endl;
			this->_pos = data.find("\"f_size\":", this->_pos);// "f_size"
			findData(data, this->_pos + 9, '}');
			folder->setFSize(std::stoi(this->_cutData));
			//std::cout << "folder = " << folder->getFSize() << std::endl;

			this->_folderMap[folder->getId()] = folder;

			if (data[this->_pos + 1] == ']')//Check if end
				return;
		}
	}
}

void	Parser::fillUser(std::string& data)
{
	unsigned int pos = data.find("\"users\":[", this->_pos);
	if (data[pos + 11] != ']') { //if no user
		//std::cout << "USER" << std::endl;
		while (42) {
			User *user = new User();
			this->_pos = data.find("\"id\":", this->_pos);// "id"
			findData(data, this->_pos + 5, ',');
			user->setId(std::stoi(this->_cutData));
			//std::cout << "id = " << user->getId() << std::endl;
			this->_pos = data.find("\"firstname\":\"", this->_pos);// "firstname"
			findData(data, this->_pos + 13, '"');
			user->setFirstName(this->_cutData);
			//std::cout << "firstName = " << user->getFirstName() << std::endl;
			this->_pos = data.find("\"lastname\":\"", this->_pos);// "lastname"
			findData(data, this->_pos + 12, '"');
			user->setLastName(this->_cutData);
			//std::cout << "lastName = " << user->getLastName() << std::endl;
			this->_pos = data.find("\"username\":\"", this->_pos);// "username"
			findData(data, this->_pos + 12, '"');
			user->setUserName(this->_cutData);
			//std::cout << "username = " << user->getUserName() << std::endl;
			this->_pos = data.find("\"email\":\"", this->_pos);// "email"
			findData(data, this->_pos + 9, '"');
			user->setEmail(this->_cutData);
			//std::cout << "email = " << user->getEmail() << std::endl;
			this->_pos = data.find("\"salt\":\"", this->_pos);// "salt"
			findData(data, this->_pos + 8, '"');
			user->setSalt(this->_cutData);
			//std::cout << "salt = " << user->getSalt() << std::endl;
			this->_pos = data.find("\"password\":\"", this->_pos);// "password"
			findData(data, this->_pos + 12, '"');
			user->setPassword(this->_cutData);
			//std::cout << "password = " << user->getPassword() << std::endl;
			this->_pos = data.find("\"confirm_password\":\"", this->_pos);// "confirm_password"
			findData(data, this->_pos + 20, '"');
			user->setConfirmPassword(this->_cutData);
			//std::cout << "confirm_password = " << user->getConfirmPassword() << std::endl;
			this->_pos = data.find("\"usertype\":\"", this->_pos);// "usertype"
			findData(data, this->_pos + 12, ']');
			user->setUserType(this->_cutData);
			//std::cout << "userType = " << user->getUserType() << std::endl;
			this->_pos = data.find("\"roles\":\"", this->_pos);// "roles"
			findData(data, this->_pos + 9, ']');
			user->setRoles(this->_cutData);
			//std::cout << "roles = " << user->getRoles() << std::endl;
			this->_pos = data.find("\"creation_date\":\"", this->_pos);// "creation_date"
			findData(data, this->_pos + 17, '"');
			user->setCreationDate(strToTime(this->_cutData));
			this->_pos = data.find("\"picture_account\":\"", this->_pos);// "picture_account"
			findData(data, this->_pos + 19, '"');
			user->setPictureAccount(this->_cutData);
			//std::cout << "picAccount = " << user->getPictureAccount() << std::endl;
			this->_pos = data.find("\"is_active\":", this->_pos);// "is_active"
			findData(data, this->_pos + 12, ',');
			this->_cutData == "true" ? user->setIsActive(true) : user->setIsActive(false);
			//std::cout << "isActive = " << user->getIsActive() << std::endl;
			this->_pos = data.find("\"question\":\"", this->_pos);// "question"
			findData(data, this->_pos + 12, '"');
			user->setQuestion(this->_cutData);
			//std::cout << "question = " << user->getQuestion() << std::endl;
			this->_pos = data.find("\"answer\":\"", this->_pos);// "answer"
			findData(data, this->_pos + 10, '"');
			user->setAnswer(this->_cutData);
			//std::cout << "answer = " << user->getAnswer() << std::endl;

			this->_userMap[user->getId()] = user;

			if (data[this->_pos + 2] == ']')//Check if end
				return;
		}
	}
}