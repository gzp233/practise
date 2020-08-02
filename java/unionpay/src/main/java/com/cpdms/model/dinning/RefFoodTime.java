package com.cpdms.model.dinning;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;

/**
 * 菜品餐段关系 RefFoodTime 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:47:39
 */
public class RefFoodTime implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 餐段id **/
    @JsonSerialize(using= StringFormat.class)
	private String timeId;
		
	/** 菜品id **/
    @JsonSerialize(using= StringFormat.class)
	private String foodId;
		
		
	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getTimeId() {
        return timeId;
    }

    public void setTimeId(String timeId) {
        this.timeId = timeId;
    }
	 
			
	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }
	 
			
	public RefFoodTime() {
        super();
    }
    
																	
	public RefFoodTime(String id,String timeId,String foodId) {
	
		this.id = id;
		this.timeId = timeId;
		this.foodId = foodId;
		
	}
	
}