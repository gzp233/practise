package com.cpdms.model.dinning;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;

/**
 * 菜品期数 FoodNumber 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:31:30
 */
public class FoodNumber implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

    @JsonSerialize(using= StringFormat.class)
    private String fnDeptId;

    public String getFnDeptId() {
        return fnDeptId;
    }

    public void setFnDeptId(String fnDeptId) {
        this.fnDeptId = fnDeptId;
    }
		
	/** 期数 **/
	private Integer number;
		
	/** 开始时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date beginTime;
		
	/** 结束时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date endTime;
		
		
	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public Integer getNumber() {
        return number;
    }

    public void setNumber(Integer number) {
        this.number = number;
    }
	 
			
	public Date getBeginTime() {
        return beginTime;
    }

    public void setBeginTime(Date beginTime) {
        this.beginTime = beginTime;
    }
	 
			
	public Date getEndTime() {
        return endTime;
    }

    public void setEndTime(Date endTime) {
        this.endTime = endTime;
    }
	 
			
	public FoodNumber() {
        super();
    }
    
																						
	public FoodNumber(String id,Integer number,Date beginTime,Date endTime) {
	
		this.id = id;
		this.number = number;
		this.beginTime = beginTime;
		this.endTime = endTime;
		
	}
	
}