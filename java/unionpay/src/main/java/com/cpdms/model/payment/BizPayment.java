package com.cpdms.model.payment;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 交易记录表 BizPayment 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:06:26
 */
public class BizPayment implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 金额 **/
	private BigDecimal amt;
		
	/** 交易id **/
    @JsonSerialize(using= StringFormat.class)
	private String payTransId;
		
	/** 交易方式 **/
    @JsonSerialize(using= StringFormat.class)
	private String payType;
		
	/** 交易类型(0:支付，1：充值，2冲正 **/
	private Integer type;
		
	/** 卡号 **/
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;
		
	/** 交易时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date payDate;
		
	/** 备注 **/
    @JsonSerialize(using= StringFormat.class)
	private String payMemo;
		
	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;
		
		
	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public BigDecimal getAmt() {
        return amt;
    }

    public void setAmt(BigDecimal amt) {
        this.amt = amt;
    }
	 
			
	public String getPayTransId() {
        return payTransId;
    }

    public void setPayTransId(String payTransId) {
        this.payTransId = payTransId;
    }
	 
			
	public String getPayType() {
        return payType;
    }

    public void setPayType(String payType) {
        this.payType = payType;
    }
	 
			
	public Integer getType() {
        return type;
    }

    public void setType(Integer type) {
        this.type = type;
    }
	 
			
	public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
    }
	 
			
	public Date getPayDate() {
        return payDate;
    }

    public void setPayDate(Date payDate) {
        this.payDate = payDate;
    }
	 
			
	public String getPayMemo() {
        return payMemo;
    }

    public void setPayMemo(String payMemo) {
        this.payMemo = payMemo;
    }
	 
			
	public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }
	 
			
	public BizPayment() {
        super();
    }
    
																																															
	public BizPayment(String id,BigDecimal amt,String payTransId,String payType,Integer type,String cardNo,Date payDate,String payMemo,Date createDate) {
	
		this.id = id;
		this.amt = amt;
		this.payTransId = payTransId;
		this.payType = payType;
		this.type = type;
		this.cardNo = cardNo;
		this.payDate = payDate;
		this.payMemo = payMemo;
		this.createDate = createDate;
		
	}
	
}