package com.cpdms.model.hair;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import java.lang.Integer;
import java.util.List;

/**
 * 美发预定订单表 BizHairOrder
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:55:57
 */
public class BizHairOrder implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 订单编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordCode;

	/** 预定时间 **/
	@NotNull(message = "预定开始时间不能为空")
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date ordTime;

	/** 预定时间 **/
	@NotNull(message = "预定结束时间不能为空")
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date ordEndTime;

    public Date getOrdEndTime() {
        return ordEndTime;
    }

    public void setOrdEndTime(Date ordEndTime) {
        this.ordEndTime = ordEndTime;
    }

    /** 完成时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date finishTime;

	/** 预定人员id **/
    @JsonSerialize(using= StringFormat.class)
	private String ordEmpId;

	/** 订单状态 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordState;

	/** 订单金额 **/
	private BigDecimal ordAmt;

	/** 订单来源 **/
	@NotBlank(message = "订单来源不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String ordSrc;

	/** 联系电话 **/
    @JsonSerialize(using= StringFormat.class)
	private String tel;

	/** 备注 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordMemo;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

    public String getBhoDeptId() {
        return bhoDeptId;
    }

    public void setBhoDeptId(String bhoDeptId) {
        this.bhoDeptId = bhoDeptId;
    }

    @JsonSerialize(using= StringFormat.class)
    private String bhoDeptId;

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

	@NotBlank(message = "卡号不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;

	private List<BizHairOrdHair> bizHairOrdHairList;

    public List<BizHairOrdHair> getBizHairOrdHairList() {
        return bizHairOrdHairList;
    }

    public void setBizHairOrdHairList(List<BizHairOrdHair> bizHairOrdHairList) {
        this.bizHairOrdHairList = bizHairOrdHairList;
    }

    public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
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


	public Date getOrdTime() {
        return ordTime;
    }

    public void setOrdTime(Date ordTime) {
        this.ordTime = ordTime;
    }


	public Date getFinishTime() {
        return finishTime;
    }

    public void setFinishTime(Date finishTime) {
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


	public BigDecimal getOrdAmt() {
        return ordAmt;
    }

    public void setOrdAmt(BigDecimal ordAmt) {
        this.ordAmt = ordAmt;
    }


	public String getOrdSrc() {
        return ordSrc;
    }

    public void setOrdSrc(String ordSrc) {
        this.ordSrc = ordSrc;
    }


	public String getTel() {
        return tel;
    }

    public void setTel(String tel) {
        this.tel = tel;
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


	public BizHairOrder() {
        super();
    }


	public BizHairOrder(String id,String ordCode,Date ordTime,Date finishTime,String ordEmpId,String ordState,BigDecimal ordAmt,String ordSrc,String tel,String ordMemo,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {

		this.id = id;
		this.ordCode = ordCode;
		this.ordTime = ordTime;
		this.finishTime = finishTime;
		this.ordEmpId = ordEmpId;
		this.ordState = ordState;
		this.ordAmt = ordAmt;
		this.ordSrc = ordSrc;
		this.tel = tel;
		this.ordMemo = ordMemo;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;

	}

}
