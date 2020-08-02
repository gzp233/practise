package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 预定时间 BaseBookTime 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:48:30
 */
public class BaseBookTime implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 菜品id **/
    @JsonSerialize(using= StringFormat.class)
	private String foodId;
		
	/** 最长可预定天数 **/
	private Integer maxDays;
		
	/** 预定提前时间 **/
	private Integer maxHours;
		
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
		
		
	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }
	 
			
	public Integer getMaxDays() {
        return maxDays;
    }

    public void setMaxDays(Integer maxDays) {
        this.maxDays = maxDays;
    }
	 
			
	public Integer getMaxHours() {
        return maxHours;
    }

    public void setMaxHours(Integer maxHours) {
        this.maxHours = maxHours;
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
	 
			
	public BaseBookTime() {
        super();
    }
    
																																															
	public BaseBookTime(String id,String foodId,Integer maxDays,Integer maxHours,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {
	
		this.id = id;
		this.foodId = foodId;
		this.maxDays = maxDays;
		this.maxHours = maxHours;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;
		
	}
	
}