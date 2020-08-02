package com.cpdms.model.examination;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import java.lang.Integer;
import java.util.List;

/**
 * 体检预约表 BizExaminationOrder
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:53:05
 */
public class BizExaminationOrder implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 订单编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordCode;

	/** 预定时间 **/
	@NotBlank(message = "预约时间不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String ordTime;

	/** 完成时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String finishTime;

	/** 预定人员id **/
    @JsonSerialize(using= StringFormat.class)
	private String ordEmpId;

    @JsonSerialize(using= StringFormat.class)
    private String beoDeptId;

    public String getBeoDeptId() {
        return beoDeptId;
    }

    public void setBeoDeptId(String beoDeptId) {
        this.beoDeptId = beoDeptId;
    }

    /** 订单状态 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordState;

	/** 订单来源 **/
	@NotBlank(message = "订单来源不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String ordSrc;

	/** 体检人姓名 **/
	@NotBlank(message = "体检姓名不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String name;

	@NotBlank(message = "部门不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String deptName;

    public String getDeptName() {
        return deptName;
    }

    public void setDeptName(String deptName) {
        this.deptName = deptName;
    }

    /** 联系电话 **/
    @JsonSerialize(using= StringFormat.class)
	private String tel;

	/** 卡号 **/
	@NotBlank(message = "卡号不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;

	/** 身份证号码 **/
    @JsonSerialize(using= StringFormat.class)
	private String certId;

	/** 备注 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordMemo;

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

	private List<BizExaminationOrdItem> bizExaminationOrdItemList;

    public List<BizExaminationOrdItem> getBizExaminationOrdItemList() {
        return bizExaminationOrdItemList;
    }

    public void setBizExaminationOrdItemList(List<BizExaminationOrdItem> bizExaminationOrdItemList) {
        this.bizExaminationOrdItemList = bizExaminationOrdItemList;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getOrdCode() {
        return ordCode;
    }

    public void setOrdCode(String ordCode) {
        this.ordCode = ordCode;
    }


	public String getOrdTime() {
        return ordTime;
    }

    public void setOrdTime(String ordTime) {
        this.ordTime = ordTime;
    }


	public String getFinishTime() {
        return finishTime;
    }

    public void setFinishTime(String finishTime) {
        this.finishTime = finishTime;
    }


	public String getOrdEmpId() {
        return ordEmpId;
    }

    public void setOrdEmpId(String ordEmpId) {
        this.ordEmpId = ordEmpId;
    }


	public String getOrdState() {
        return ordState;
    }

    public void setOrdState(String ordState) {
        this.ordState = ordState;
    }


	public String getOrdSrc() {
        return ordSrc;
    }

    public void setOrdSrc(String ordSrc) {
        this.ordSrc = ordSrc;
    }


	public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }


	public String getTel() {
        return tel;
    }

    public void setTel(String tel) {
        this.tel = tel;
    }


	public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
    }


	public String getCertId() {
        return certId;
    }

    public void setCertId(String certId) {
        this.certId = certId;
    }


	public String getOrdMemo() {
        return ordMemo;
    }

    public void setOrdMemo(String ordMemo) {
        this.ordMemo = ordMemo;
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


	public BizExaminationOrder() {
        super();
    }


	public BizExaminationOrder(String id,String ordCode,String ordTime,String finishTime,String ordEmpId,String ordState,String ordSrc,String name,String tel,String cardNo,String certId,String ordMemo,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {

		this.id = id;
		this.ordCode = ordCode;
		this.ordTime = ordTime;
		this.finishTime = finishTime;
		this.ordEmpId = ordEmpId;
		this.ordState = ordState;
		this.ordSrc = ordSrc;
		this.name = name;
		this.tel = tel;
		this.cardNo = cardNo;
		this.certId = certId;
		this.ordMemo = ordMemo;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;

	}

}
