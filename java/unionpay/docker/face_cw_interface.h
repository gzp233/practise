#ifndef _CW_INTERFACE_H_
#define _CW_INTERFACE_H_

#ifdef _MSC_VER
#ifdef __cplusplus
#ifdef SDK_EXPORTS
#define CW_SDK_API  extern "C" __declspec(dllexport)
#else
#define CW_SDK_API extern "C" __declspec(dllimport) 
#endif
#else
#ifdef SDK_EXPORTS
#define CW_SDK_API __declspec(dllexport)
#else
#define CW_SDK_API __declspec(dllimport)
#endif
#endif
#else /* _MSC_VER */
#ifdef __cplusplus
#ifdef SDK_EXPORTS
#define CW_SDK_API  extern "C" __attribute__((visibility ("default")))
#else
#define CW_SDK_API extern "C"
#endif
#else
#ifdef SDK_EXPORTS
#define CW_SDK_API 
#else
#define CW_SDK_API __attribute__((visibility ("default")))
#endif
#endif
#endif


#define QUA_THR 0.7
#define SKIN_THR 0.3
#define MAX_NUM_FACES 50
#define LEN_FEAT_FC_CW 2056*2              /* 云从人脸特征长度 */
#define LEN_BASE64_STR 3*1024*1024      /* base64图片长度 */
#define LEN_FEA_VER 10 

#define ORDER_NONE 0                    /* 输出不排序 */
#define ORDER_BIG_FACE_FIRST 1          /* 大脸优先 */
#define ORDER_BIG_ID_FIRST 2            /* 大ID优先 */
#define ORDER_LEFT_FIRST 3              /* 大脸优先 */
#define ORDER_UPSIDE_FIRST 4            /* 大脸优先 */

typedef struct cw_feature
{
    int fea_len;
    char feat[LEN_FEAT_FC_CW+1];
    char* face_info_hack;
} cw_feature_t;

CW_SDK_API int face_cw_feature_get_init(void** p_fea, const char *config_path);
CW_SDK_API int face_cw_det_init(void** p_det, const char *config_path);
CW_SDK_API int face_cw_hack_init(void ** p_hack, const char *config_path);
CW_SDK_API int face_cw_get_feature(const char * pic_nm, char * feature, int *f_len, void *p_det, void* p_fea, char* fea_ver);
CW_SDK_API int face_cw_hack_detect(const char *pic_nm, void* p_det, void *p_hack, float *p_score);
CW_SDK_API int face_cw_feature_get_free(void** p_fea);
CW_SDK_API int face_cw_det_free(void** p_det);
CW_SDK_API int face_cw_hack_free(void ** p_hack);

#endif
