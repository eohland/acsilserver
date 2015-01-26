#include <iostream>
#include <ctime>


class File
{
public:
	File();
	~File();
	unsigned int	getId();
	void			setId(unsigned int id);
	std::string		&getRealPath();
	void			setRealPath(std::string&);
	std::string		&getChosenPath();
	void			setChosenPath(std::string&);
	unsigned int	getSize();
	void			setSize(unsigned int);
	std::string		getName();
	void			setName(std::string&);
	std::string		&getPath();
	void			setPath(std::string&);
	std::string		&getOwner();
	void			setOwner(std::string&);
	std::string		&getPseudoOwner();
	void			setPseudoOwner(std::string&);
	std::string		getUploadDate();
	void			setUploadDate(std::string&);
	std::string		getLastModifDate();
	void			setLastModifDate(std::string&);
	unsigned int	getIsProfilePicture();
	void			setIsProfilePicture(unsigned int);
	unsigned int	getIsShared();
	void			setIsShared(unsigned int);
	std::string		&getMimeType();
	void			setMimeType(std::string&);
	std::string		&getFormatedSize();
	void			setFormatedSize(std::string&);
	unsigned int	getFolder();
	void			setFolder(unsigned int folder);
	std::string		&getSharedFileUserInfos();
	void			setSharedFileUserInfos(std::string&);

private:
	unsigned int	_id;
	std::string		_real_path;
	std::string		_chosen_path;
	unsigned int	_size;
	std::string		_name;
	std::string		_path;
	std::string		_owner;
	std::string		_pseudo_owner;
	std::string		_upload_date;
	std::string		_last_modif_date;
	unsigned int	_is_profile_picture;
	unsigned int	_is_shared;
	std::string		_mime_type;
	std::string		_formated_size;
	unsigned int	_folder;
	std::string		_sharedFileUserInfos;
};