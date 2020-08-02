package com.cpdms.model.personnel;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import java.lang.Integer;

/**
 * 行员信息 BasePersonnel 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2020-01-11 10:42:23
 */
public class BasePersonnel implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 员工编号 **/
    @JsonSerialize(using= StringFormat.class)
	private String personnelNo;
		
	/** 饭卡号 **/
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;
		
	/** 部门名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String deptName;

    @JsonSerialize(using= StringFormat.class)
    private String bpDeptId;

    public String getBpDeptId() {
        return bpDeptId;
    }

    public void setBpDeptId(String bpDeptId) {
        this.bpDeptId = bpDeptId;
    }

    public String getUnitName() {
        return unitName;
    }

    public void setUnitName(String unitName) {
        this.unitName = unitName;
    }

    @JsonSerialize(using= StringFormat.class)
    private String unitName;
		
	/** 人员姓名 **/
    @JsonSerialize(using= StringFormat.class)
	private String personnelName;
		
	/** 手机号 **/
    @JsonSerialize(using= StringFormat.class)
	private String personnelMobile;
		
	/** 入职时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date entryTime;
		
	/** 特征值计算结果（0：未计算，1成功，2失败 **/
	private Integer calcResult;

    @JsonSerialize(using= StringFormat.class)
    private String calcValue;

	/** 应答码 **/
    @JsonSerialize(using= StringFormat.class)
	private String respCode;
		
	/** 应答信息 **/
    @JsonSerialize(using= StringFormat.class)
	private String respMsg;
		
	/** 同步状态（0：未同步，1已同步 **/
	private Integer syncStatus;
		
	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;
		
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

    /** 临时存放图片信息 **/
    @JsonSerialize(using= StringFormat.class)
    private String photoName;

    public String getPhotoName() {
        return photoName;
    }

    public void setPhotoName(String photoName) {
        this.photoName = photoName;
    }

    public String getCalcValue() {
        return calcValue;
    }

    public void setCalcValue(String calcValue) {
        this.calcValue = calcValue;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getPersonnelNo() {
        return personnelNo;
    }

    public void setPersonnelNo(String personnelNo) {
        this.personnelNo = personnelNo;
    }
	 
			
	public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
    }
	 
			
	public String getDeptName() {
        return deptName;
    }

    public void setDeptName(String deptName) {
        this.deptName = deptName;
    }
	 
			
	public String getPersonnelName() {
        return personnelName;
    }

    public void setPersonnelName(String personnelName) {
        this.personnelName = personnelName;
    }
	 
			
	public String getPersonnelMobile() {
        return personnelMobile;
    }

    public void setPersonnelMobile(String personnelMobile) {
        this.personnelMobile = personnelMobile;
    }
	 
			
	public Date getEntryTime() {
        return entryTime;
    }

    public void setEntryTime(Date entryTime) {
        this.entryTime = entryTime;
    }
	 
			
	public Integer getCalcResult() {
        return calcResult;
    }

    public void setCalcResult(Integer calcResult) {
        this.calcResult = calcResult;
    }
	 
			
	public String getRespCode() {
        return respCode;
    }

    public void setRespCode(String respCode) {
        this.respCode = respCode;
    }
	 
			
	public String getRespMsg() {
        return respMsg;
    }

    public void setRespMsg(String respMsg) {
        this.respMsg = respMsg;
    }
	 
			
	public Integer getSyncStatus() {
        return syncStatus;
    }

    public void setSyncStatus(Integer syncStatus) {
        this.syncStatus = syncStatus;
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
	 
			
	public BasePersonnel() {
        super();
    }
    
																																																																																		
	public BasePersonnel(String id,String personnelNo,String cardNo,String deptName,String personnelName,String personnelMobile,Date entryTime,Integer calcResult,String respCode,String respMsg,Integer syncStatus,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {
	
		this.id = id;
		this.personnelNo = personnelNo;
		this.cardNo = cardNo;
		this.deptName = deptName;
		this.personnelName = personnelName;
		this.personnelMobile = personnelMobile;
		this.entryTime = entryTime;
		this.calcResult = calcResult;
		this.respCode = respCode;
		this.respMsg = respMsg;
		this.syncStatus = syncStatus;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;
		
	}
	
}