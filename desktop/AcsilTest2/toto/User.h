#include <iostream>
#include <ctime>

class User
{
public:
	User();
	~User();
	unsigned int	getId();
	void			setId(unsigned int);
	std::string		&getFirstName();
	void			setFirstName(std::string&);
	std::string		&getLastName();
	void			setLastName(std::string&);
	std::string		&getUserName();
	void			setUserName(std::string&);
	std::string		&getEmail();
	void			setEmail(std::string&);
	std::string		&getSalt();
	void			setSalt(std::string&);
	std::string		&getPassword();
	void			setPassword(std::string&);
	std::string		&getConfirmPassword();
	void			setConfirmPassword(std::string&);
	std::string		&getUserType();
	void			setUserType(std::string&);
	std::string		&getRoles();
	void			setRoles(std::string&);
	std::time_t		getCreationDate();
	void			setCreationDate(std::time_t);
	std::string		&getPictureAccount();
	void			setPictureAccount(std::string&);
	bool			getIsActive();
	void			setIsActive(bool);
	std::string		&getQuestion();
	void			setQuestion(std::string&);
	std::string		&getAnswer();
	void			setAnswer(std::string&);

private:
	unsigned int	_id;
	std::string		_firstName;
	std::string		_lastName;
	std::string		_userName;
	std::string		_email;
	std::string		_salt;
	std::string		_password;
	std::string		_confirm_password;
	std::string		_user_type;
	std::string		_roles;
	std::time_t		_creation_date;
	std::string		_picture_account;
	bool			_is_active;
	std::string		_question;
	std::string		_answer;
};