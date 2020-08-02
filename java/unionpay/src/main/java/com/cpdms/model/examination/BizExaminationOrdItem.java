package com.cpdms.model.examination;

import java.io.Serializable;
import java.util.Date;
import java.util.List;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;

/**
 * 体检预约项目表 BizExaminationOrdItem
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:57:03
 */
public class BizExaminationOrdItem implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 项目ID **/
	@NotBlank(message = "项目ID不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String examinationId;

	/** 订单ID **/
    @JsonSerialize(using= StringFormat.class)
	private String examinationOrdId;

	/** 医院名 **/
	@NotBlank(message = "医院名称不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String hospitalName;

	/** 体检项目名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String examinationName;

	/** 开始时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String startTime;

	/** 结束时间 **/
    @JsonSerialize(using= StringFormat.class)
	private String endTime;

	private List<BaseExaminationItem> baseExaminationItemList;

    public List<BaseExaminationItem> getBaseExaminationItemList() {
        return baseExaminationItemList;
    }

    public void setBaseExaminationItemList(List<BaseExaminationItem> baseExaminationItemList) {
        this.baseExaminationItemList = baseExaminationItemList;
    }

    private BaseExamination baseExamination;

    public BaseExamination getBaseExamination() {
        return baseExamination;
    }

    public void setBaseExamination(BaseExamination baseExamination) {
        this.baseExamination = baseExamination;
    }

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


	public String getExaminationOrdId() {
        return examinationOrdId;
    }

    public void setExaminationOrdId(String examinationOrdId) {
        this.examinationOrdId = examinationOrdId;
    }


	public String getHospitalName() {
        return hospitalName;
    }

    public void setHospitalName(String hospitalName) {
        this.hospitalName = hospitalName;
    }


	public String getExaminationName() {
        return examinationName;
    }

    public void setExaminationName(String examinationName) {
        this.examinationName = examinationName;
    }


	public String getStartTime() {
        return startTime;
    }

    public void setStartTime(String startTime) {
        this.startTime = startTime;
    }


	public String getEndTime() {
        return endTime;
    }

    public void setEndTime(String endTime) {
        this.endTime = endTime;
    }


	public BizExaminationOrdItem() {
        super();
    }


	public BizExaminationOrdItem(String id,String examinationId,String examinationOrdId,String hospitalName,String examinationName,String startTime,String endTime) {

		this.id = id;
		this.examinationId = examinationId;
		this.examinationOrdId = examinationOrdId;
		this.hospitalName = hospitalName;
		this.examinationName = examinationName;
		this.startTime = startTime;
		this.endTime = endTime;

	}

}
