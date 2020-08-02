package com.cpdms.model.examination;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;

/**
 *  RefExaminationItem
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:36
 */
public class RefExaminationItem implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 套餐ID **/
    @JsonSerialize(using= StringFormat.class)
	private String examinationId;

	/** 项目ID **/
    @JsonSerialize(using= StringFormat.class)
	private String examinationItemId;


	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getExaminationId() {
        return examinationId;
    }

    public void setExaminationId(String examinationId) {
        this.examinationId = examinationId;
    }


	public String getExaminationItemId() {
        return examinationItemId;
    }

    public void setExaminationItemId(String examinationItemId) {
        this.examinationItemId = examinationItemId;
    }


	public RefExaminationItem() {
        super();
    }


	public RefExaminationItem(String id,String examinationId,String examinationItemId) {

		this.id = id;
		this.examinationId = examinationId;
		this.examinationItemId = examinationItemId;

	}

}
