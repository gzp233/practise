package authservice

import (
	"single/models"

	"golang.org/x/crypto/bcrypt"
)

type Auth struct {
	ID       int
	Email    string
	Password string
}

// check if user can access
func (a *Auth) Check() (bool, error) {
	user, err := models.GetUserByEmail(a.Email)
	if err != nil {
		return false, err
	}
	if user.ID <= 0 {
		return false, nil
	}
	if err = bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(a.Password)); err != nil {
		return false, nil
	}
	a.ID = user.ID

	return true, nil
}
