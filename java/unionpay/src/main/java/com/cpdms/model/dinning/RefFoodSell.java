package com.cpdms.model.dinning;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;

/**
 * 菜品供应方式关系 RefFoodSell 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:40:08
 */
public class RefFoodSell implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 供应方式id **/
    @JsonSerialize(using= StringFormat.class)
	private String sellTypeId;
		
	/** 菜品id **/
    @JsonSerialize(using= StringFormat.class)
	private String foodId;

	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getSellTypeId() {
        return sellTypeId;
    }

    public void setSellTypeId(String sellTypeId) {
        this.sellTypeId = sellTypeId;
    }
	 
			
	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }
	 
			
	public RefFoodSell() {
        super();
    }
    
																	
	public RefFoodSell(String id,String sellTypeId,String foodId) {
	
		this.id = id;
		this.sellTypeId = sellTypeId;
		this.foodId = foodId;
		
	}
	
}