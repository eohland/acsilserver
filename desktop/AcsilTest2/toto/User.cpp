#include "User.h"

User::User()
{
}
User::~User()
{
}

unsigned int	User::getId()
{
	return(this->_id);
}

void			User::setId(unsigned int id)
{
	this->_id = id;
}

std::string		&User::getFirstName()
{
	return(this->_firstName);
}

void			User::setFirstName(std::string &firstName)
{
	this->_firstName = firstName;
}

std::string		&User::getLastName()
{
	return(this->_lastName);
}

void			User::setLastName(std::string &lastName)
{
	this->_lastName = lastName;
}

std::string		&User::getUserName()
{
	return(this->_userName);
}

void			User::setUserName(std::string &userName)
{
	this->_userName = userName;
}

	std::string		&User::getEmail()
{
	return(this->_email);
}

void			User::setEmail(std::string &email)
{
	this->_email = email;
}

std::string		&User::getSalt()
{
	return(this->_salt);
}

void			User::setSalt(std::string &salt)
{
	this->_salt = salt;
}

std::string		&User::getPassword()
{
	return(this->_password);
}

void			User::setPassword(std::string &password)
{
	this->_password = password;
}

std::string		&User::getConfirmPassword()
{
	return(this->_confirm_password);
}

void			User::setConfirmPassword(std::string &confirm_password)
{
	this->_confirm_password = confirm_password;
}

std::string		&User::getUserType()
{
	return(this->_user_type);
}

void			User::setUserType(std::string &user_type)
{
	this->_user_type = user_type;
}

std::string		&User::getRoles()
{
	return(this->_roles);
}

void			User::setRoles(std::string &roles)
{
	this->_roles = roles;
}

std::time_t		User::getCreationDate()
{
	return(this->_creation_date);
}

void			User::setCreationDate(std::time_t creation_date)
{
	this->_creation_date = creation_date;
}

std::string		&User::getPictureAccount()
{
	return(this->_picture_account);
}

void			User::setPictureAccount(std::string &picture_account)
{
	this->_picture_account = picture_account;
}

bool			User::getIsActive()
{
	return(this->_is_active);
}
void			User::setIsActive(bool is_active)
{
	this->_is_active = is_active;
}

std::string		&User::getQuestion()
{
	return(this->_question);
}

void			User::setQuestion(std::string &question)
{
	this->_question = question;
}

std::string		&User::getAnswer()
{
	return(this->_answer);
}

void			User::setAnswer(std::string &answer)
{
	this->_answer = answer;
}