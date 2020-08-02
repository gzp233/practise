package com.cpdms.model.examination;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;
import java.util.List;

/**
 * 基础体检项目表 BaseExamination
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:40
 */
public class BaseExamination implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 医院ID **/
    @JsonSerialize(using= StringFormat.class)
	private String hospitalId;

    @JsonSerialize(using= StringFormat.class)
    private String beDeptId;

    public String getBeDeptId() {
        return beDeptId;
    }

    public void setBeDeptId(String beDeptId) {
        this.beDeptId = beDeptId;
    }

    /** 项目名 **/
    @JsonSerialize(using= StringFormat.class)
	private String examinationName;

	/** 可预约数量 **/
	private Integer num;

	private Integer orderNum;

    private BaseHospital baseHospital;

    public BaseHospital getBaseHospital() {
        return baseHospital;
    }

    public void setBaseHospital(BaseHospital baseHospital) {
        this.baseHospital = baseHospital;
    }

    /** 开始时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String startTime;

	/** 结束时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String endTime;

	/** 状态（0 inactive 1 active **/
	private Integer status;

	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

	/** 更新时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

	/** 更新人 **/
    @JsonSerialize(using= StringFormat.class)
	private String updateBy;

	private List<BaseExaminationItem> baseExaminationItemList;

    public List<BaseExaminationItem> getBaseExaminationItemList() {
        return baseExaminationItemList;
    }

    public void setBaseExaminationItemList(List<BaseExaminationItem> baseExaminationItemList) {
        this.baseExaminationItemList = baseExaminationItemList;
    }

    public Integer getOrderNum() {
        return orderNum;
    }

    public void setOrderNum(Integer orderNum) {
        this.orderNum = orderNum;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getHospitalId() {
        return hospitalId;
    }

    public void setHospitalId(String hospitalId) {
        this.hospitalId = hospitalId;
    }


	public String getExaminationName() {
        return examinationName;
    }

    public void setExaminationName(String examinationName) {
        this.examinationName = examinationName;
    }


	public Integer getNum() {
        return num;
    }

    public void setNum(Integer num) {
        this.num = num;
    }


	public String getStartTime() {
        return startTime;
    }

    public void setStartTime(String startTime) {
        this.startTime = startTime;
    }


	public String getEndTime() {
        return endTime;
    }

    public void setEndTime(String endTime) {
        this.endTime = endTime;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }


	public String getCreateBy() {
        return createBy;
    }

    public void setCreateBy(String createBy) {
        this.createBy = createBy;
    }


	public Date getUpdateDate() {
        return updateDate;
    }

    public void setUpdateDate(Date updateDate) {
        this.updateDate = updateDate;
    }


	public String getUpdateBy() {
        return updateBy;
    }

    public void setUpdateBy(String updateBy) {
        this.updateBy = updateBy;
    }


	public BaseExamination() {
        super();
    }


	public BaseExamination(String id,String hospitalId,String examinationName,Integer num,String startTime,String endTime,Integer status,Date createDate,String createBy,Date updateDate,String updateBy) {

		this.id = id;
		this.hospitalId = hospitalId;
		this.examinationName = examinationName;
		this.num = num;
		this.startTime = startTime;
		this.endTime = endTime;
		this.status = status;
		this.createDate = createDate;
		this.createBy = createBy;
		this.updateDate = updateDate;
		this.updateBy = updateBy;

	}

}
