package com.cpdms.model.auto;

import java.io.Serializable;
import java.lang.Integer;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

/**
 * 定时任务调度表 SysQuartzJob 
 * @author fuce_自动生成
 * @email 115889198@qq.com
 * @date 2019-09-13 00:03:35
 */
public class SysQuartzJob implements Serializable {

	private static final long serialVersionUID = 1L;
	
		
	/** id **/
	@JsonSerialize(using= StringFormat.class)
	private String id;
		
	/** 任务名称 **/
	@JsonSerialize(using= StringFormat.class)
	private String jobName;
		
	/** 任务组名 **/
	@JsonSerialize(using= StringFormat.class)
	private String jobGroup;
		
	/** 调用目标字符串 **/
	@JsonSerialize(using= StringFormat.class)
	private String invokeTarget;
		
	/** cron执行表达式 **/
	@JsonSerialize(using= StringFormat.class)
	private String cronExpression;
		
	/** cron计划策略 **/
	@JsonSerialize(using= StringFormat.class)
	private String misfirePolicy;
		
	/** 是否并发执行（0允许 1禁止） **/
	@JsonSerialize(using= StringFormat.class)
	private String concurrent;
		
	/** 任务状态（0正常 1暂停） **/
	private Integer status;
		
		
	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }
	 
			
	public String getJobName() {
        return jobName;
    }

    public void setJobName(String jobName) {
        this.jobName = jobName;
    }
	 
			
	public String getJobGroup() {
        return jobGroup;
    }

    public void setJobGroup(String jobGroup) {
        this.jobGroup = jobGroup;
    }
	 
			
	public String getInvokeTarget() {
        return invokeTarget;
    }

    public void setInvokeTarget(String invokeTarget) {
        this.invokeTarget = invokeTarget;
    }
	 
			
	public String getCronExpression() {
        return cronExpression;
    }

    public void setCronExpression(String cronExpression) {
        this.cronExpression = cronExpression;
    }
	 
			
	public String getMisfirePolicy() {
        return misfirePolicy;
    }

    public void setMisfirePolicy(String misfirePolicy) {
        this.misfirePolicy = misfirePolicy;
    }
	 
			
	public String getConcurrent() {
        return concurrent;
    }

    public void setConcurrent(String concurrent) {
        this.concurrent = concurrent;
    }
	 
			
	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }
	 
			
	public SysQuartzJob() {
        super();
    }
    
																																										
	public SysQuartzJob(String id,String jobName,String jobGroup,String invokeTarget,String cronExpression,String misfirePolicy,String concurrent,Integer status) {
	
		this.id = id;
		this.jobName = jobName;
		this.jobGroup = jobGroup;
		this.invokeTarget = invokeTarget;
		this.cronExpression = cronExpression;
		this.misfirePolicy = misfirePolicy;
		this.concurrent = concurrent;
		this.status = status;
		
	}
	
}