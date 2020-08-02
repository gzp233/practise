package com.cpdms.model.hair;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;
import java.lang.Integer;

/**
 * 服务类型表 BaseHairSrv
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 15:26:29
 */
public class BaseHairSrv implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 服务名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String serviceName;

	/** 状态(1 active 0 inactive **/
	private Integer status;

    @JsonSerialize(using= StringFormat.class)
    private String bhsDeptId;

    public String getBhsDeptId() {
        return bhsDeptId;
    }

    public void setBhsDeptId(String bhsDeptId) {
        this.bhsDeptId = bhsDeptId;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getServiceName() {
        return serviceName;
    }

    public void setServiceName(String serviceName) {
        this.serviceName = serviceName;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public BaseHairSrv() {
        super();
    }


	public BaseHairSrv(String id,String serviceName,Integer status) {

		this.id = id;
		this.serviceName = serviceName;
		this.status = status;

	}

}
