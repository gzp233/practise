package com.cpdms.model.auto;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;
import java.util.Date;

public class TSysDept implements Serializable {

    private static final long serialVersionUID = 1L;
    @JsonSerialize(using= StringFormat.class)
    private String id;
    @JsonSerialize(using= StringFormat.class)
    private String deptName;
    @JsonSerialize(using= StringFormat.class)
    private String deptCode;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getDeptName() {
        return deptName;
    }

    public void setDeptName(String deptName) {
        this.deptName = deptName;
    }

    public String getDeptCode() {
        return deptCode;
    }

    public void setDeptCode(String deptCode) {
        this.deptCode = deptCode;
    }

    public String getDeptExtCode() {
        return deptExtCode;
    }

    public void setDeptExtCode(String deptExtCode) {
        this.deptExtCode = deptExtCode;
    }

    public String getParentId() {
        return parentId;
    }

    public void setParentId(String parentId) {
        this.parentId = parentId;
    }

    public int getDelFlag() {
        return delFlag;
    }

    public void setDelFlag(int delFlag) {
        this.delFlag = delFlag;
    }

    public String getCreateBy() {
        return createBy;
    }

    public void setCreateBy(String createBy) {
        this.createBy = createBy;
    }

    public String getUpdateBy() {
        return updateBy;
    }

    public void setUpdateBy(String updateBy) {
        this.updateBy = updateBy;
    }

    public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }

    public Date getUpdateDate() {
        return updateDate;
    }

    public void setUpdateDate(Date updateDate) {
        this.updateDate = updateDate;
    }
    @JsonSerialize(using= StringFormat.class)
    private String deptExtCode;
    @JsonSerialize(using= StringFormat.class)
    private String parentId;

    private int delFlag;
    @JsonSerialize(using= StringFormat.class)
    private String createBy;
    @JsonSerialize(using= StringFormat.class)
    private String updateBy;
    @JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
    private Date createDate;
    @JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
    private Date updateDate;





}
