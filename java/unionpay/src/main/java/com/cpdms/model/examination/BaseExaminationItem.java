package com.cpdms.model.examination;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;

/**
 *  BaseExaminationItem
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:02
 */
public class BaseExaminationItem implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 项目名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String itemName;

	/** 项目描述 **/
    @JsonSerialize(using= StringFormat.class)
	private String itemDesc;

	private Boolean ischeck;

    public Boolean getIscheck() {
        return ischeck;
    }

    public void setIscheck(Boolean ischeck) {
        this.ischeck = ischeck;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getItemName() {
        return itemName;
    }

    public void setItemName(String itemName) {
        this.itemName = itemName;
    }


	public String getItemDesc() {
        return itemDesc;
    }

    public void setItemDesc(String itemDesc) {
        this.itemDesc = itemDesc;
    }


	public BaseExaminationItem() {
        super();
    }


	public BaseExaminationItem(String id,String itemName,String itemDesc) {

		this.id = id;
		this.itemName = itemName;
		this.itemDesc = itemDesc;

	}

}
