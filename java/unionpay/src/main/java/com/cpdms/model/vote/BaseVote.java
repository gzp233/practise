package com.cpdms.model.vote;

import java.io.Serializable;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import java.lang.Integer;
import java.util.List;

/**
 *  BaseVote
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:40:53
 */
public class BaseVote implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	private Integer num;

    @JsonSerialize(using= StringFormat.class)
    private String bvDeptId;

    public String getBvDeptId() {
        return bvDeptId;
    }

    public void setBvDeptId(String bvDeptId) {
        this.bvDeptId = bvDeptId;
    }

    public Integer getNum() {
        return num;
    }

    public void setNum(Integer num) {
        this.num = num;
    }

    /** 投票主题 **/
    @JsonSerialize(using= StringFormat.class)
	private String voteName;

	/** 投票形式（0 单选 1 多选 **/
	private Integer voteType;

	/** 投票描述 **/
    @JsonSerialize(using= StringFormat.class)
	private String voteDesc;

	/** 开始时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date startTime;

	/** 截止日期 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date endTime;

	/** 投票状态（进行中 已完成 **/
    @JsonSerialize(using= StringFormat.class)
	private String voteStatus;

	/** 状态（1active 0 inactive **/
	private Integer status;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/** 修改人 **/
    @JsonSerialize(using= StringFormat.class)
	private String updateBy;

	/** 修改时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

	private List<BaseVoteItem> baseVoteItemList;

    public List<BaseVoteItem> getBaseVoteItemList() {
        return baseVoteItemList;
    }

    public void setBaseVoteItemList(List<BaseVoteItem> baseVoteItemList) {
        this.baseVoteItemList = baseVoteItemList;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getVoteName() {
        return voteName;
    }

    public void setVoteName(String voteName) {
        this.voteName = voteName;
    }


	public Integer getVoteType() {
        return voteType;
    }

    public void setVoteType(Integer voteType) {
        this.voteType = voteType;
    }


	public String getVoteDesc() {
        return voteDesc;
    }

    public void setVoteDesc(String voteDesc) {
        this.voteDesc = voteDesc;
    }


	public Date getStartTime() {
        return startTime;
    }

    public void setStartTime(Date startTime) {
        this.startTime = startTime;
    }


	public Date getEndTime() {
        return endTime;
    }

    public void setEndTime(Date endTime) {
        this.endTime = endTime;
    }


	public String getVoteStatus() {
        return voteStatus;
    }

    public void setVoteStatus(String voteStatus) {
        this.voteStatus = voteStatus;
    }


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public String getCreateBy() {
        return createBy;
    }

    public void setCreateBy(String createBy) {
        this.createBy = createBy;
    }


	public Date getCreateDate() {
        return createDate;
    }

    public void setCreateDate(Date createDate) {
        this.createDate = createDate;
    }


	public String getUpdateBy() {
        return updateBy;
    }

    public void setUpdateBy(String updateBy) {
        this.updateBy = updateBy;
    }


	public Date getUpdateDate() {
        return updateDate;
    }

    public void setUpdateDate(Date updateDate) {
        this.updateDate = updateDate;
    }


	public BaseVote() {
        super();
    }


	public BaseVote(String id,String voteName,Integer voteType,String voteDesc,Date startTime,Date endTime,String voteStatus,Integer status,String createBy,Date createDate,String updateBy,Date updateDate) {

		this.id = id;
		this.voteName = voteName;
		this.voteType = voteType;
		this.voteDesc = voteDesc;
		this.startTime = startTime;
		this.endTime = endTime;
		this.voteStatus = voteStatus;
		this.status = status;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;

	}

}
