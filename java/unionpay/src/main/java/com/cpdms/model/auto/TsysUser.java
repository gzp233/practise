package com.cpdms.model.auto;

import java.io.Serializable;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

public class TsysUser implements Serializable {
	@JsonSerialize(using= StringFormat.class)
    private String id;
	@JsonSerialize(using= StringFormat.class)
    private String username;
	@JsonSerialize(using= StringFormat.class)
    private String password;
	@JsonSerialize(using= StringFormat.class)
    private String nickname;

    public String getDeptId() {
        return deptId;
    }

    public void setDeptId(String deptId) {
        this.deptId = deptId;
    }

    @JsonSerialize(using= StringFormat.class)
	private String deptId;

    private static final long serialVersionUID = 1L;

    public TsysUser(String id, String username, String password, String nickname,String deptId) {
        this.id = id;
        this.username = username;
        this.password = password;
        this.nickname = nickname;
        this.deptId = deptId;
    }

    public TsysUser() {
        super();
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id == null ? null : id.trim();
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username == null ? null : username.trim();
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password == null ? null : password.trim();
    }

    public String getNickname() {
        return nickname;
    }

    public void setNickname(String nickname) {
        this.nickname = nickname == null ? null : nickname.trim();
    }
}