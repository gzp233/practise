package com.cpdms.model.dinning;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import java.lang.Integer;

/**
 * 每日菜品 DailyDishes
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:15:11
 */
public class DailyDishes implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 菜品期数id **/
	@NotBlank(message = "期数ID不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String groupId;
    @JsonSerialize(using= StringFormat.class)
	private String ddDeptId;

	/** 菜品id **/
	@NotBlank(message = "菜品ID不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String foodId;

	/** 菜品名称 **/
	@NotBlank(message = "菜品名不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String foodName;

	/** 价格 **/
	@NotNull(message = "价格不能为空")
	private BigDecimal foodPrice;

	/** 售卖方式1online0offline **/
	@NotNull(message = "售卖方式不能为空")
	private Integer foodStatus;

	/** 供应方式 **/
    @JsonSerialize(using= StringFormat.class)
	private String sellTypeName;

	/** 供应方式id **/
    @JsonSerialize(using= StringFormat.class)
	private String sellTypeIds;

	/** 餐段 **/
    @JsonSerialize(using= StringFormat.class)
	private String rangeName;

	/** 餐段id **/
    @JsonSerialize(using= StringFormat.class)
	private String rangeIds;

	/** 菜品开始时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
    @NotNull(message = "菜品开始时间不能为空")
	private Date foodBeginTime;

	/** 菜品结束时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
    @NotNull(message = "菜品结束时间不能为空")
	private Date foodEndTime;

	/** 创建时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date createDate;

	/** 更新时间 **/
	@JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
	private Date updateDate;

	/** 创建人 **/
    @JsonSerialize(using= StringFormat.class)
	private String createBy;

	/** 修改人 **/
    @JsonSerialize(using= StringFormat.class)
	private String updateBy;

	private FoodNumber foodNumber;

    @JsonSerialize(using= StringFormat.class)
	private String imgPath;
    @JsonSerialize(using= StringFormat.class)
	private String thumbImgPath;

    public String getThumbImgPath() {
        return thumbImgPath;
    }

    public void setThumbImgPath(String thumbImgPath) {
        this.thumbImgPath = thumbImgPath;
    }

    @JsonSerialize(using= StringFormat.class)
    private String foodDesc;
    @JsonSerialize(using= StringFormat.class)
	private String foodUnit;

	private Integer status;

    public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }

    public String getFoodUnit() {
        return foodUnit;
    }

    public void setFoodUnit(String foodUnit) {
        this.foodUnit = foodUnit;
    }

    public String getFoodDesc() {
        return foodDesc;
    }

    public void setFoodDesc(String foodDesc) {
        this.foodDesc = foodDesc;
    }

    public String getImgPath() {
        return imgPath;
    }

    public void setImgPath(String imgPath) {
        this.imgPath = imgPath;
    }

    public FoodNumber getFoodNumber() {
        return foodNumber;
    }

    public void setFoodNumber(FoodNumber foodNumber) {
        this.foodNumber = foodNumber;
    }

    /* 评价 */
	private Double avgFraction;
	private Double avgStar;

    public Double getAvgFraction() {
        return avgFraction;
    }

    public void setAvgFraction(Double avgFraction) {
        this.avgFraction = avgFraction;
    }

    public Double getAvgStar() {
        return avgStar;
    }

    public void setAvgStar(Double avgStar) {
        this.avgStar = avgStar;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getGroupId() {
        return groupId;
    }

    public void setGroupId(String groupId) {
        this.groupId = groupId;
    }


	public String getDdDeptId() {
		return ddDeptId;
	}

	public void setDdDeptId(String ddDeptId) {
		this.ddDeptId = ddDeptId;
	}

	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }


	public String getFoodName() {
        return foodName;
    }

    public void setFoodName(String foodName) {
        this.foodName = foodName;
    }


	public BigDecimal getFoodPrice() {
        return foodPrice;
    }

    public void setFoodPrice(BigDecimal foodPrice) {
        this.foodPrice = foodPrice;
    }


	public Integer getFoodStatus() {
        return foodStatus;
    }

    public void setFoodStatus(Integer foodStatus) {
        this.foodStatus = foodStatus;
    }


	public String getSellTypeName() {
        return sellTypeName;
    }

    public void setSellTypeName(String sellTypeName) {
        this.sellTypeName = sellTypeName;
    }


	public String getSellTypeIds() {
        return sellTypeIds;
    }

    public void setSellTypeIds(String sellTypeIds) {
        this.sellTypeIds = sellTypeIds;
    }


	public String getRangeName() {
        return rangeName;
    }

    public void setRangeName(String rangeName) {
        this.rangeName = rangeName;
    }


	public String getRangeIds() {
        return rangeIds;
    }

    public void setRangeIds(String rangeIds) {
        this.rangeIds = rangeIds;
    }


	public Date getFoodBeginTime() {
        return foodBeginTime;
    }

    public void setFoodBeginTime(Date foodBeginTime) {
        this.foodBeginTime = foodBeginTime;
    }


	public Date getFoodEndTime() {
        return foodEndTime;
    }

    public void setFoodEndTime(Date foodEndTime) {
        this.foodEndTime = foodEndTime;
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


	public DailyDishes() {
        super();
    }


	public DailyDishes(String id,String groupId,String foodId,String foodName,BigDecimal foodPrice,Integer foodStatus,String sellTypeName,String sellTypeIds,String rangeName,String rangeIds,Date foodBeginTime,Date foodEndTime,Date createDate,Date updateDate,String createBy,String updateBy) {

		this.id = id;
		this.groupId = groupId;
		this.foodId = foodId;
		this.foodName = foodName;
		this.foodPrice = foodPrice;
		this.foodStatus = foodStatus;
		this.sellTypeName = sellTypeName;
		this.sellTypeIds = sellTypeIds;
		this.rangeName = rangeName;
		this.rangeIds = rangeIds;
		this.foodBeginTime = foodBeginTime;
		this.foodEndTime = foodEndTime;
		this.createDate = createDate;
		this.updateDate = updateDate;
		this.createBy = createBy;
		this.updateBy = updateBy;

	}

}
