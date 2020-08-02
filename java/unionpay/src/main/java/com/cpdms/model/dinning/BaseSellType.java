package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 菜品供应方式 BaseSellType 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:53
 */
public class BaseSellType implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
	@JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 供应方式 **/
	@JsonSerialize(using= StringFormat.class)
	private String sellTypeName;
		
	/** 供应方式编码 **/
	@JsonSerialize(using= StringFormat.class)
	private String sellTypeCode;
	
	@JsonSerialize(using= StringFormat.class)
	private String bstDeptId;
		
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
	 
			
	public String getSellTypeName() {
        return sellTypeName;
    }

    public void setSellTypeName(String sellTypeName) {
        this.sellTypeName = sellTypeName;
    }
	 
			
	public String getSellTypeCode() {
        return sellTypeCode;
    }

    public void setSellTypeCode(String sellTypeCode) {
        this.sellTypeCode = sellTypeCode;
    }
			
	public String getBstDeptId() {
		return bstDeptId;
	}

	public void setBstDeptId(String bstDeptId) {
		this.bstDeptId = bstDeptId;
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
	 
			
	public BaseSellType() {
        super();
    }
    
																																										
	public BaseSellType(String id,String sellTypeName,String sellTypeCode,String createBy,Date createDate,String updateBy,Date updateDate,Integer status) {
	
		this.id = id;
		this.sellTypeName = sellTypeName;
		this.sellTypeCode = sellTypeCode;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;
		
	}
	
}