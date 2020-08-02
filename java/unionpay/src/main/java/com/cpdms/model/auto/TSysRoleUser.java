package com.cpdms.model.auto;

import java.io.Serializable;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

public class TSysRoleUser implements Serializable {
	@JsonSerialize(using= StringFormat.class)
    private String id;
	@JsonSerialize(using= StringFormat.class)
    private String sysUserId;
	@JsonSerialize(using= StringFormat.class)
    private String sysRoleId;

    private static final long serialVersionUID = 1L;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id == null ? null : id.trim();
    }

    public String getSysUserId() {
        return sysUserId;
    }

    public void setSysUserId(String sysUserId) {
        this.sysUserId = sysUserId == null ? null : sysUserId.trim();
    }

    public String getSysRoleId() {
        return sysRoleId;
    }

    public void setSysRoleId(String sysRoleId) {
        this.sysRoleId = sysRoleId == null ? null : sysRoleId.trim();
    }

	public TSysRoleUser() {
		super();
	}

	public TSysRoleUser(String id, String sysUserId, String sysRoleId) {
		super();
		this.id = id;
		this.sysUserId = sysUserId;
		this.sysRoleId = sysRoleId;
	}
    
    
}