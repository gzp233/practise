package com.cpdms.model.hair;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.cpdms.model.dinning.ImgUrl;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;
import java.util.List;

/**
 * 美发项目表 BaseHair
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:57:06
 */
public class BaseHair implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 美发项目ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 项目名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String hairName;

	/** 类型（0 剪发，1烫发，2染发，3护理 **/
	private Integer hairType;

	/** 价格 **/
	private BigDecimal hairPrice;

	/** 图片id **/
    @JsonSerialize(using= StringFormat.class)
	private String imgId;

    public String getBhDeptId() {
        return bhDeptId;
    }

    public void setBhDeptId(String bhDeptId) {
        this.bhDeptId = bhDeptId;
    }

    @JsonSerialize(using= StringFormat.class)
    private String bhDeptId;

	/** 描述 **/
    @JsonSerialize(using= StringFormat.class)
	private String hairDesc;

	/** 状态（1active 0 inactive **/
	private Integer status;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/** 修改人 **/
    @JsonSerialize(using= StringFormat.class)
	private String updateBy;

	/** 修改人 **/
    @JsonSerialize(using= StringFormat.class)
	private String serviceId;

	private BaseHairSrv baseHairSrv;

    public BaseHairSrv getBaseHairSrv() {
        return baseHairSrv;
    }

    public void setBaseHairSrv(BaseHairSrv baseHairSrv) {
        this.baseHairSrv = baseHairSrv;
    }

    public String getServiceId() {
        return serviceId;
    }

    public void setServiceId(String serviceId) {
        this.serviceId = serviceId;
    }

    /** 修改时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

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


	public String getHairName() {
        return hairName;
    }

    public void setHairName(String hairName) {
        this.hairName = hairName;
    }


	public Integer getHairType() {
        return hairType;
    }

    public void setHairType(Integer hairType) {
        this.hairType = hairType;
    }


	public BigDecimal getHairPrice() {
        return hairPrice;
    }

    public void setHairPrice(BigDecimal hairPrice) {
        this.hairPrice = hairPrice;
    }


	public String getImgId() {
        return imgId;
    }

    public void setImgId(String imgId) {
        this.imgId = imgId;
    }


	public String getHairDesc() {
        return hairDesc;
    }

    public void setHairDesc(String hairDesc) {
        this.hairDesc = hairDesc;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
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


	public BaseHair() {
        super();
    }


	public BaseHair(String id,String hairName,Integer hairType,BigDecimal hairPrice,String imgId,String hairDesc,Integer status,String createBy,Date createDate,String updateBy,Date updateDate) {

		this.id = id;
		this.hairName = hairName;
		this.hairType = hairType;
		this.hairPrice = hairPrice;
		this.imgId = imgId;
		this.hairDesc = hairDesc;
		this.status = status;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;

	}

}
