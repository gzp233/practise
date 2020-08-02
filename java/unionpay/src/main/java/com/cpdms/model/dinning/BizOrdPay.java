package com.cpdms.model.dinning;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;
import com.fasterxml.jackson.annotation.JsonFormat;

/**
 * 订单支付 BizOrdPay 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:04:59
 */
public class BizOrdPay implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
	private String id;
		
	/** 订单id **/
	private String ordId;
		
	/** 订单编码 **/
	private String ordCode;
	
	private String bopDeptId;
		
	/** 金额 **/
	private BigDecimal amt;
		
	/** 支付交易id **/
	private String payTransId;
		
	/** 支付方式 **/
	private String payType;
		
	/** 支付卡号 **/
	private String cardNo;
		
	/** 支付时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date payDate;
		
	/** 备注 **/
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
	 
			
	public String getOrdId() {
        return ordId;
    }

    public void setOrdId(String ordId) {
        this.ordId = ordId;
    }
	 
			
	public String getOrdCode() {
        return ordCode;
    }

    public void setOrdCode(String ordCode) {
        this.ordCode = ordCode;
    }
	 
			
	public String getBopDeptId() {
		return bopDeptId;
	}

	public void setBopDeptId(String bopDeptId) {
		this.bopDeptId = bopDeptId;
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
	 
			
	public BizOrdPay() {
        super();
    }
    
																																																				
	public BizOrdPay(String id,String ordId,String ordCode,BigDecimal amt,String payTransId,String payType,String cardNo,Date payDate,String payMemo,Date createDate) {
	
		this.id = id;
		this.ordId = ordId;
		this.ordCode = ordCode;
		this.amt = amt;
		this.payTransId = payTransId;
		this.payType = payType;
		this.cardNo = cardNo;
		this.payDate = payDate;
		this.payMemo = payMemo;
		this.createDate = createDate;
		
	}
	
}