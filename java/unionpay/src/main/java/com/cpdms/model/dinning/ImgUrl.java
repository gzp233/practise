package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 图片路径 ImgUrl
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 13:24:45
 */
public class ImgUrl implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 图片id **/
    @JsonSerialize(using= StringFormat.class)
	private String imgId;

	/** 图片路径 **/
    @JsonSerialize(using= StringFormat.class)
	private String imgPath;
    @JsonSerialize(using= StringFormat.class)
	private String thumbImgPath;

	/** 图片顺序 **/
	private Integer imgSeq;

	/** 上传人 **/
    @JsonSerialize(using= StringFormat.class)
	private String uploadBy;

	/** imageId **/
    @JsonSerialize(using= StringFormat.class)
	private String imageId;

    public String getThumbImgPath() {
        return thumbImgPath;
    }

    public void setThumbImgPath(String thumbImgPath) {
        this.thumbImgPath = thumbImgPath;
    }

    public String getImageId() {
		return imageId;
	}

	public void setImageId(String imageId) {
		this.imageId = imageId;
	}


	/** 上传时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date uploadDate;


	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getImgId() {
        return imgId;
    }

    public void setImgId(String imgId) {
        this.imgId = imgId;
    }


	public String getImgPath() {
        return imgPath;
    }

    public void setImgPath(String imgPath) {
        this.imgPath = imgPath;
    }


	public Integer getImgSeq() {
        return imgSeq;
    }

    public void setImgSeq(Integer imgSeq) {
        this.imgSeq = imgSeq;
    }


	public String getUploadBy() {
        return uploadBy;
    }

    public void setUploadBy(String uploadBy) {
        this.uploadBy = uploadBy;
    }


	public Date getUploadDate() {
        return uploadDate;
    }

    public void setUploadDate(Date uploadDate) {
        this.uploadDate = uploadDate;
    }


	public ImgUrl() {
        super();
    }


	public ImgUrl(String id,String imgId,String imgPath,Integer imgSeq,String uploadBy,Date uploadDate) {

		this.id = id;
		this.imgId = imgId;
		this.imgPath = imgPath;
		this.imgSeq = imgSeq;
		this.uploadBy = uploadBy;
		this.uploadDate = uploadDate;

	}

}
