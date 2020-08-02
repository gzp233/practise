package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 餐段设置 BaseTimeRange 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:18
 */
public class BaseTimeRange implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
	@JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 名称 **/
	@JsonSerialize(using= StringFormat.class)
	private String rangeName;
		
	/** 编码 **/
	@JsonSerialize(using= StringFormat.class)
	private String rangeCode;
	
	@JsonSerialize(using= StringFormat.class)
	private String btrDeptId;
		
	/** 开始时间 **/
	@JsonSerialize(using= StringFormat.class)
	private String startTime;
		
	/** 结束时间 **/
	@JsonSerialize(using= StringFormat.class)
	private String endDate;
		
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
	
	private boolean ischeck;//判断是否又这个权限
		
	public boolean isIscheck() {
		return ischeck;
	}

	public void setIscheck(boolean ischeck) {
		this.ischeck = ischeck;
	}

	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getRangeName() {
        return rangeName;
    }

    public void setRangeName(String rangeName) {
        this.rangeName = rangeName;
    }
	 
			
	public String getRangeCode() {
        return rangeCode;
    }

    public void setRangeCode(String rangeCode) {
        this.rangeCode = rangeCode;
    }
	
			
	public String getBtrDeptId() {
		return btrDeptId;
	}

	public void setBtrDeptId(String btrDeptId) {
		this.btrDeptId = btrDeptId;
	}

	public String getStartTime() {
        return startTime;
    }

    public void setStartTime(String startTime) {
        this.startTime = startTime;
    }
	 
			
	public String getEndDate() {
        return endDate;
    }

    public void setEndDate(String endDate) {
        this.endDate = endDate;
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
	 
			
	public BaseTimeRange() {
        super();
    }
    
																																																				
	public BaseTimeRange(String id,String rangeName,String rangeCode,String startTime,String endDate,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {
	
		this.id = id;
		this.rangeName = rangeName;
		this.rangeCode = rangeCode;
		this.startTime = startTime;
		this.endDate = endDate;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;
		
	}
	
}