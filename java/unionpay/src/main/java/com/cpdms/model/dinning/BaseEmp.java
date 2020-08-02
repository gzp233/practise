package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 员工信息，云闪付给数据 BaseEmp
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:51:18
 */
public class BaseEmp implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

    @JsonSerialize(using= StringFormat.class)
	private String orgCode;

	/** 部门id **/
    @JsonSerialize(using= StringFormat.class)
	private String beDeptId;

    public String getBeDeptId() {
        return beDeptId;
    }

    public void setBeDeptId(String beDeptId) {
        this.beDeptId = beDeptId;
    }

    /** 部门编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String deptCode;

	/** 部门名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String deptName;

	/** 人员编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String empCode;

	/** 人员姓名 **/
    @JsonSerialize(using= StringFormat.class)
	private String empName;

	/** 手机号 **/
    @JsonSerialize(using= StringFormat.class)
	private String empMobile;

	/** 卡号 **/
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

    @JsonSerialize(using= StringFormat.class)
	private String openId;
    @JsonSerialize(using= StringFormat.class)
	private String accessToken;
	private Long   expiredAt;

    @JsonSerialize(using= StringFormat.class)
	private String encryptCardNo;

    public String getEncryptCardNo() {
        return encryptCardNo;
    }

    public void setEncryptCardNo(String encryptCardNo) {
        this.encryptCardNo = encryptCardNo;
    }

    public String getOpenId() {
        return openId;
    }

    public void setOpenId(String openId) {
        this.openId = openId;
    }

    public String getAccessToken() {
        return accessToken;
    }

    public void setAccessToken(String accessToken) {
        this.accessToken = accessToken;
    }

    public Long getExpiredAt() {
        return expiredAt;
    }

    public void setExpiredAt(Long expiredAt) {
        this.expiredAt = expiredAt;
    }

    /** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/** 修改人 **/
    @JsonSerialize(using= StringFormat.class)
	private String updateBy;

	/** 修改时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

	/** 1--active,0--inactive **/
	private Integer status;


	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getOrgCode() {
		return orgCode;
	}

	public void setOrgCode(String orgCode) {
		this.orgCode = orgCode;
	}



	public String getDeptCode() {
        return deptCode;
    }

    public void setDeptCode(String deptCode) {
        this.deptCode = deptCode;
    }



    public String getDeptName() {
		return deptName;
	}

	public void setDeptName(String deptName) {
		this.deptName = deptName;
	}


	public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }


	public String getEmpName() {
        return empName;
    }

    public void setEmpName(String empName) {
        this.empName = empName;
    }


	public String getEmpMobile() {
        return empMobile;
    }

    public void setEmpMobile(String empMobile) {
        this.empMobile = empMobile;
    }


	public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
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


	public BaseEmp() {
        super();
    }


	public BaseEmp(String id,String deptCode,String deptName,String empCode,String empName,String empMobile,String cardNo,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {

		this.id = id;
		this.deptCode = deptCode;
		this.deptName = deptName;
		this.empCode = empCode;
		this.empName = empName;
		this.empMobile = empMobile;
		this.cardNo = cardNo;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;

	}

}
