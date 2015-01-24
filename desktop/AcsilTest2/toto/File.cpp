#include "File.h"

File::File()
{
}

File::~File()
{
}

unsigned int	File::getId()
{
	return(this->_id);
}

void			File::setId(unsigned int id)
{
	this->_id = id;
}

std::string		&File::getRealPath()
{
	return(this->_real_path);
}

void			File::setRealPath(std::string &real_path)
{
	this->_real_path = real_path;
}

std::string		&File::getChosenPath()
{
	return(this->_chosen_path);
}

void			File::setChosenPath(std::string &chosen_path)
{
	this->_chosen_path = chosen_path;
}

unsigned int	File::getSize()
{
	return(this->_size);
}

void			File::setSize(unsigned int size)
{
	this->_size = size;
}

std::string		File::getName()
{
	return(this->_name);
}

void			File::setName(std::string &name)
{
	this->_name = name;
}

std::string		&File::getPath()
{
	return(this->_path);
}

void			File::setPath(std::string &path)
{
	this->_path = path;
}

std::string		&File::getOwner()
{
	return(this->_owner);
}

void			File::setOwner(std::string &owner)
{
	this->_owner = owner;
}

std::string		&File::getPseudoOwner()
{
	return(this->_pseudo_owner);
}

void			File::setPseudoOwner(std::string &pseudo_owner)
{
	this->_pseudo_owner = pseudo_owner;
}

std::string		File::getUploadDate()
{
	return(this->_upload_date);
}

void			File::setUploadDate(std::string &upload_date)
{
	this->_upload_date = upload_date.substr(0, 19).replace(10, 1, " ").replace(4, 1, ":").replace(7, 1, ":");
}

std::string		File::getLastModifDate()
{
	return(this->_last_modif_date);
}

void			File::setLastModifDate(std::string &last_modif_date)
{
	this->_last_modif_date = last_modif_date.substr(0, 19).replace(10, 1, " ").replace(4, 1, ":").replace(7, 1, ":");
}

unsigned int	File::getIsProfilePicture()
{
	return(this->_is_profile_picture);
}

void			File::setIsProfilePicture(unsigned int is_profile_picture)
{
	this->_is_profile_picture = is_profile_picture;
}

unsigned int	File::getIsShared()
{
	return(this->_is_shared);
}

void			File::setIsShared(unsigned int is_shared)
{
	this->_is_shared = is_shared;
}

std::string		&File::getMimeType()
{
	return(this->_mime_type);
}

void			File::setMimeType(std::string &mime_type)
{
	this->_mime_type = mime_type;
}

std::string		&File::getFormatedSize()
{
	return(this->_formated_size);
}

void			File::setFormatedSize(std::string &formated_size)
{
	this->_formated_size = formated_size;
}

unsigned int	File::getFolder()
{
	return(this->_folder);
}

void			File::setFolder(unsigned int folder)
{
	this->_folder = folder;
}

std::string		&File::getSharedFileUserInfos()
{
	return(this->_sharedFileUserInfos);
}

void			File::setSharedFileUserInfos(std::string& shareInfo)
{
	this->_sharedFileUserInfos = shareInfo;
}