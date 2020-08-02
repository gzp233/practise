package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;
import java.util.List;

/**
 * 厨师表 BaseCook
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-06 14:25:23
 */
public class BaseCook implements Serializable {

	private static final long serialVersionUID = 1L;


	/**  **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 厨师姓名 **/
    @JsonSerialize(using= StringFormat.class)
	private String cookName;

	/** 图片ID **/
    @JsonSerialize(using= StringFormat.class)
	private String imgId;

	/**  **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

    @JsonSerialize(using= StringFormat.class)
    private String bcDeptId;

    public String getBcDeptId() {
        return bcDeptId;
    }

    public void setBcDeptId(String bcDeptId) {
        this.bcDeptId = bcDeptId;
    }

    /* 厨师描述 */
    @JsonSerialize(using= StringFormat.class)
    private String intro;

    public String getIntro() {
        return intro;
    }

    public void setIntro(String intro) {
        this.intro = intro;
    }

    /**  **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/**  **/
	private String updateBy;

	/**  **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

	/** 1active 0inactive **/
	private Integer status;

	private List<ImgUrl> imgs;

    public List<ImgUrl> getImgs() {
        return imgs;
    }

    public void setImgs(List<ImgUrl> imgs) {
        this.imgs = imgs;
    }


	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getCookName() {
        return cookName;
    }

    public void setCookName(String cookName) {
        this.cookName = cookName;
    }


	public String getImgId() {
        return imgId;
    }

    public void setImgId(String imgId) {
        this.imgId = imgId;
    }


	public String getCreateBy() {
        return createBy;
    }

    public void setCreateBy(String createBy) {
        this.createBy = createBy;
    }


	public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }


	public String getUpdateBy() {
        return updateBy;
    }

    public void setUpdateBy(String updateBy) {
        this.updateBy = updateBy;
    }


	public Date getUpdateDate() {
        return updateDate;
    }

    public void setUpdateDate(Date updateDate) {
        this.updateDate = updateDate;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public BaseCook() {
        super();
    }


	public BaseCook(String id,String cookName,String imgId,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {

		this.id = id;
		this.cookName = cookName;
		this.imgId = imgId;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;

	}

}
