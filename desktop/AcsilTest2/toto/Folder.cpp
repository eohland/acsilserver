#include "Folder.h"

Folder::Folder()
{
}

Folder::~Folder()
{
}

unsigned int	Folder::getId()
{
	return(this->_id);
}

void			Folder::setId(unsigned int id)
{
	this->_id = id;
}

std::string		&Folder::getRealPath()
{
	return(this->_real_path);
}

void			Folder::setRealPath(std::string &real_path)
{
	this->_real_path = real_path;
}

std::string		&Folder::getChosenPath()
{
	return(this->_chosen_path);
}

void			Folder::setChosenPath(std::string &chosen_path)
{
	this->_chosen_path = chosen_path;
}

unsigned int	Folder::getSize()
{
	return(this->_size);
}

void			Folder::setSize(unsigned int size)
{
	this->_size = size;
}

std::string		&Folder::getName()
{
	return(this->_name);
}

void			Folder::setName(std::string &name)
{
	this->_name = name;
}

std::string		&Folder::getPath()
{
	return(this->_path);
}

void			Folder::setPath(std::string &path)
{
	this->_path = path;
}

std::string		&Folder::getOwner()
{
	return(this->_owner);
}

void			Folder::setOwner(std::string &owner)
{
	this->_owner = owner;
}

std::string		&Folder::getPseudoOwner()
{
	return(this->_pseudo_owner);
}

void			Folder::setPseudoOwner(std::string &pseudo_owner)
{
	this->_pseudo_owner = pseudo_owner;
}

std::string		&Folder::getUploadDate()
{
	return(this->_upload_date);
}

void			Folder::setUploadDate(std::string &upload_date)
{
	this->_upload_date = upload_date.substr(0, 19).replace(10, 1, " ").replace(4, 1, ":").replace(7, 1, ":");
}

std::string		&Folder::getLastModifDate()
{
	return(this->_last_modif_date);
}

void			Folder::setLastModifDate(std::string &last_modif_date)
{
	this->_last_modif_date = last_modif_date.substr(0, 19).replace(10, 1, " ").replace(4, 1, ":").replace(7, 1, ":");
}

unsigned int	Folder::getParentFolder()
{
	return(this->_parent_folder);
}

void			Folder::setParentFolder(unsigned int parent_folder)
{
	this->_parent_folder = parent_folder;
}

unsigned int			Folder::getIsShared()
{
	return(this->_is_shared);
}

void			Folder::setIsShared(unsigned int is_shared)
{
	this->_is_shared = is_shared;
}


unsigned int	Folder::getFSize()
{
	return(this->_f_size);
}

void			Folder::setFSize(unsigned int f_size)
{
	this->_f_size = f_size;
}