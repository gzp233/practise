package util

import (
	"fmt"
	"os"
	"path"
)

// CheckExist check if the file exists
func CheckExist(src string) bool {
	_, err := os.Stat(src)

	return os.IsExist(err)
}

// CheckPermission check if the file has permission
func CheckPermission(src string) bool {
	_, err := os.Stat(src)

	return os.IsPermission(err)
}

// GetSize get the file size
func GetSize(fileName string) (int64, error) {
	fileInfo, err := os.Stat(fileName)
	if err != nil {
		return -1, err
	}

	return fileInfo.Size(), nil
}

// GetExt get the file ext
func GetExt(fileName string) string {
	return path.Ext(fileName)
}

// MkDir create a directory
func MkDir(src string) error {
	return os.MkdirAll(src, os.ModePerm)
}

// IsNotExistMkDir create a directory if it does not exist
func IsNotExistMkDir(src string) error {
	if exist := CheckExist(src); !exist {
		return MkDir(src)
	}

	return nil
}

// Open a file according to a specific mode
func Open(name string, flag int, perm os.FileMode) (*os.File, error) {
	return os.OpenFile(name, flag, perm)
}

// MustOpen maximize trying to open the file
func MustOpen(fileName, filePath string) (*os.File, error) {
	dir, err := os.Getwd()
	if err != nil {
		return nil, fmt.Errorf("os.Getwd err: %v", err)
	}

	src := path.Join(dir, filePath)
	if perm := CheckPermission(src); perm {
		return nil, fmt.Errorf("file permission err: %v", err)
	}

	if err = IsNotExistMkDir(src); err != nil {
		return nil, fmt.Errorf("make dir err: %v", err)
	}

	f, err := Open(path.Join(src, fileName), os.O_APPEND|os.O_CREATE|os.O_RDWR, 0644)
	if err != nil {
		return nil, fmt.Errorf("fail to open file, err: %v", err)
	}

	return f, nil
}
