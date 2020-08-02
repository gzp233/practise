package com.cpdms.model.dinning;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;
import java.util.List;
import java.util.Map;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

/**
 * 预定订单 BizOrder
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:42:45
 */
public class BizOrder implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 订单编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordCode;
    @JsonSerialize(using= StringFormat.class)
    private String boDeptId;
	private Integer takeNo;

    public Integer getTakeNo() {
        return takeNo;
    }

    public void setTakeNo(Integer takeNo) {
        this.takeNo = takeNo;
    }

    /** 预定时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date ordTime;

	/** 预定人员id **/
    @JsonSerialize(using= StringFormat.class)
	private String ordEmpId;

	/** 预定餐段id  **/
    @JsonSerialize(using= StringFormat.class)
	private String ordTimeRangeId;

	/** 订单状态 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordState;

	/** 订单金额 **/
	private BigDecimal ordAmt;

	/** 订单来源 **/
	@NotBlank(message = "订单来源不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String ordSrc;

	/** 取餐人 **/
    @JsonSerialize(using= StringFormat.class)
	private String takeBy;

	/** 联系电话 **/
    @JsonSerialize(using= StringFormat.class)
	private String tel;

    @NotBlank(message = "卡号不能为空")
    @JsonSerialize(using= StringFormat.class)
    private String cardNo;

    public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
    }

    /** 备注 **/
    @JsonSerialize(using= StringFormat.class)
	private String ordMemo;

	/** 预定取餐时间 **/
	@NotNull(message = "预定取餐时间不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String planedTakeTime;

	/** 实际取餐时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String actualTakeTime;

	/*售卖方式*/
    @NotBlank(message = "售卖方式不能为空")
    @JsonSerialize(using= StringFormat.class)
    private String sellTypeName;

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

    @JsonSerialize(using= StringFormat.class)
	private String empName;
    @JsonSerialize(using= StringFormat.class)
	private String rangeName;

	public String getEmpName() {
		return empName;
	}

	public void setEmpName(String empName) {
		this.empName = empName;
	}

	public String getRangeName() {
		return rangeName;
	}

	public void setRangeName(String rangeName) {
		this.rangeName = rangeName;
	}

    public String getSellTypeName() {
        return sellTypeName;
    }

    public void setSellTypeName(String sellTypeName) {
        this.sellTypeName = sellTypeName;
    }

    private List<BizOrdFood> bizOrdFoodList;

    public List<BizOrdFood> getBizOrdFoodList() {
        return bizOrdFoodList;
    }


    public void setBizOrdFoodList(List<BizOrdFood> bizOrdFoodList) {
        this.bizOrdFoodList = bizOrdFoodList;
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


	public String getBoDeptId() {
		return boDeptId;
	}

	public void setBoDeptId(String boDeptId) {
		this.boDeptId = boDeptId;
	}

	public Date getOrdTime() {
        return ordTime;
    }

    public void setOrdTime(Date ordTime) {
        this.ordTime = ordTime;
    }


	public String getOrdEmpId() {
        return ordEmpId;
    }

    public void setOrdEmpId(String ordEmpId) {
        this.ordEmpId = ordEmpId;
    }


	public String getOrdTimeRangeId() {
        return ordTimeRangeId;
    }

    public void setOrdTimeRangeId(String ordTimeRangeId) {
        this.ordTimeRangeId = ordTimeRangeId;
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


	public String getTakeBy() {
        return takeBy;
    }

    public void setTakeBy(String takeBy) {
        this.takeBy = takeBy;
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


	public String getPlanedTakeTime() {
        return planedTakeTime;
    }

    public void setPlanedTakeTime(String planedTakeTime) {
        this.planedTakeTime = planedTakeTime;
    }


	public String getActualTakeTime() {
        return actualTakeTime;
    }

    public void setActualTakeTime(String actualTakeTime) {
        this.actualTakeTime = actualTakeTime;
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


	public BizOrder() {
        super();
    }


	public BizOrder(String id,String ordCode,Date ordTime,String ordEmpId,String ordTimeRangeId,String ordState,BigDecimal ordAmt,String ordSrc,String takeBy,String tel,String ordMemo,String planedTakeTime,String actualTakeTime,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {

		this.id = id;
		this.ordCode = ordCode;
		this.ordTime = ordTime;
		this.ordEmpId = ordEmpId;
		this.ordTimeRangeId = ordTimeRangeId;
		this.ordState = ordState;
		this.ordAmt = ordAmt;
		this.ordSrc = ordSrc;
		this.takeBy = takeBy;
		this.tel = tel;
		this.ordMemo = ordMemo;
		this.planedTakeTime = planedTakeTime;
		this.actualTakeTime = actualTakeTime;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;

	}

}
