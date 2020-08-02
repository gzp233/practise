package main

import (
	"flag"
	"fmt"
	"os"
	"unsafe"
)

/*
#cgo CFLAGS: -I./
#cgo LDFLAGS: -L./ -lcwinterface
#include "face_cw_interface.h"
*/
import "C"

var f string

func init() {
	flag.StringVar(&f, "f", "", "set image path")
}

func main() {
	var (
	pDet unsafe.Pointer
	pFea unsafe.Pointer
	)
	flag.Parse()
	if f == "" {
		os.Exit(1)
	}

	detCfgPath := C.CString("/root/FaceFeature/cloudwalk/etc/detect_config.cfg")
	ret := C.face_cw_det_init(&pDet,detCfgPath)
	if int(ret) < 0 {
		os.Exit(1)
	}
	feagetCfgPath := C.CString("/root/FaceFeature/cloudwalk/etc/feaget_config.cfg")
	ret = C.face_cw_feature_get_init(&pFea,feagetCfgPath)
	if int(ret) < 0 {
		os.Exit(1)
	}
	fLen := C.int(0)
	fvIndex := C.LEN_FEA_VER + 1
	fIndex := C.LEN_FEAT_FC_CW + 1
	var fv, ft string
	for i := 0;i<fvIndex;i++ {
		fv += " "
	}
	for i := 0;i<fIndex;i++ {
		ft += " "
	}
	feaVar := C.CString(fv)
	feature := C.CString(ft)
	imgPath := C.CString(f)
	ret = C.face_cw_get_feature(imgPath, feature, &fLen, pDet, pFea, feaVar)
	if int(ret) < 0 {
		os.Exit(1)
	}
	ret = C.face_cw_det_free(&pDet)
	if int(ret) < 0 {
		os.Exit(1)
	}
	ret = C.face_cw_feature_get_free(&pFea)
	if int(ret) < 0 {
		os.Exit(1)
	}
	fmt.Println(C.GoString(feature))
}