package com.cpdms.model.hair;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

/**
 *  BaseHairSetting
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-06 15:39:29
 */
public class BaseHairSetting implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 设置名 **/
    @JsonSerialize(using= StringFormat.class)
	private String settingKey;

	/** 设置值 **/
    @JsonSerialize(using= StringFormat.class)
	private String settingValue;

	/** 设置描述 **/
    @JsonSerialize(using= StringFormat.class)
	private String settingDesc;

	/** 生效时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String startTime;

	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

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


	public String getSettingKey() {
        return settingKey;
    }

    public void setSettingKey(String settingKey) {
        this.settingKey = settingKey;
    }


	public String getSettingValue() {
        return settingValue;
    }

    public void setSettingValue(String settingValue) {
        this.settingValue = settingValue;
    }


	public String getSettingDesc() {
        return settingDesc;
    }

    public void setSettingDesc(String settingDesc) {
        this.settingDesc = settingDesc;
    }


	public String getStartTime() {
        return startTime;
    }

    public void setStartTime(String startTime) {
        this.startTime = startTime;
    }


	public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }


	public BaseHairSetting() {
        super();
    }


	public BaseHairSetting(String id,String settingKey,String settingValue,String settingDesc,String startTime,Date createDate) {

		this.id = id;
		this.settingKey = settingKey;
		this.settingValue = settingValue;
		this.settingDesc = settingDesc;
		this.startTime = startTime;
		this.createDate = createDate;

	}

}
