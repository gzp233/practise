package util

import (
	"time"

	"github.com/dgrijalva/jwt-go"

	"single/pkg/setting"
)

var JwtSecret = []byte(setting.AppSetting.JwtSecret)

type Claims struct {
	Email    string `json:"email"`
	Id       int    `json:"id"`
	Password string `json:"password"`
	jwt.StandardClaims
}

// GenerateToken generate tokens used for auth
func GenerateToken(id int, email, password string) (string, error) {
	nowTime := time.Now()
	expireTime := nowTime.Add(setting.AppSetting.JwtExpireMinute)
	claims := Claims{
		email,
		id,
		EncodeMD5(password),
		jwt.StandardClaims{
			ExpiresAt: expireTime.Unix(),
			Issuer:    "woxihuanjilu",
		},
	}

	tokenClaims := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	token, err := tokenClaims.SignedString(JwtSecret)
	return token, err
}

// ParseToken parsing token
func ParseToken(token string) (*Claims, error) {
	tokenClaims, err := jwt.ParseWithClaims(token, &Claims{}, func(token *jwt.Token) (interface{}, error) {
		return JwtSecret, nil
	})

	if tokenClaims != nil {
		if claims, ok := tokenClaims.Claims.(*Claims); ok && tokenClaims.Valid {
			return claims, nil
		}
	}

	return nil, err
}
