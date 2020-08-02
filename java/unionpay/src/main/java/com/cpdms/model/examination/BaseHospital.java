package com.cpdms.model.examination;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;
import java.lang.Integer;

/**
 * 医院列表 BaseHospital
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:17
 */
public class BaseHospital implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 医院名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String hospitalName;

	/** 状态（1 active 0 inacvtive **/
	private Integer status;

    public String getBhDeptId() {
        return bhDeptId;
    }

    public void setBhDeptId(String bhDeptId) {
        this.bhDeptId = bhDeptId;
    }

    @JsonSerialize(using= StringFormat.class)
    private String bhDeptId;



	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getHospitalName() {
        return hospitalName;
    }

    public void setHospitalName(String hospitalName) {
        this.hospitalName = hospitalName;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public BaseHospital() {
        super();
    }


	public BaseHospital(String id,String hospitalName,Integer status) {

		this.id = id;
		this.hospitalName = hospitalName;
		this.status = status;

	}

}
