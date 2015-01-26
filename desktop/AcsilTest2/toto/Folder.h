#include <iostream>
#include <ctime>

class Folder
{
public:
	Folder();
	~Folder();
	unsigned int	getId();
	void			setId(unsigned int id);
	std::string		&getRealPath();
	void			setRealPath(std::string&);
	std::string		&getChosenPath();
	void			setChosenPath(std::string&);
	unsigned int	getSize();
	void			setSize(unsigned int);
	std::string		&getName();
	void			setName(std::string&);
	std::string		&getPath();
	void			setPath(std::string&);
	std::string		&getOwner();
	void			setOwner(std::string&);
	std::string		&getPseudoOwner();
	void			setPseudoOwner(std::string&);
	std::string		&getUploadDate();
	void			setUploadDate(std::string&);
	std::string		&getLastModifDate();
	void			setLastModifDate(std::string&);
	unsigned int	getParentFolder();
	void			setParentFolder(unsigned int);
	unsigned int	getIsShared();
	void			setIsShared(unsigned int);
	unsigned int	getFSize();
	void			setFSize(unsigned int f_size);

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
	unsigned int	_parent_folder;
	unsigned int	_is_shared;
	unsigned int	_f_size;
};