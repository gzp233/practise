package com.cpdms.model.dinning;

import java.io.Serializable;
import java.math.BigDecimal;
import java.util.Date;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.lang.Integer;
import java.util.List;

/**
 * 菜品设置 BaseFood
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:50:37
 */
public class BaseFood implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 菜品编码 **/
    @JsonSerialize(using= StringFormat.class)
	private String foodCode;

	/** 图片id **/
    @JsonSerialize(using= StringFormat.class)
	private String imgId;

	/** 菜品名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String foodName;

	/** 计量单位 **/
    @JsonSerialize(using= StringFormat.class)
	private String foodUnit;

	/** 价格 **/
	private BigDecimal foodPrice;

	/** 包装 **/
    @JsonSerialize(using= StringFormat.class)
	private String foodPack;

	/** 规格 **/
    @JsonSerialize(using= StringFormat.class)
	private String foodSpec;

	/** 售卖方式1online0offline **/
	private Integer foodStatus;

    @JsonSerialize(using= StringFormat.class)
	private String foodDesc;

    @JsonSerialize(using= StringFormat.class)
    private String bfDeptId;

    public String getBfDeptId() {
        return bfDeptId;
    }

    public void setBfDeptId(String bfDeptId) {
        this.bfDeptId = bfDeptId;
    }

    @JsonSerialize(using= StringFormat.class)
	private String rangeNames;
    @JsonSerialize(using= StringFormat.class)
	private String sellTypeNames;

    public String getRangeNames() {
        return rangeNames;
    }

    public void setRangeNames(String rangeNames) {
        this.rangeNames = rangeNames;
    }

    public String getSellTypeNames() {
        return sellTypeNames;
    }

    public void setSellTypeNames(String sellTypeNames) {
        this.sellTypeNames = sellTypeNames;
    }

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

	/** 1--active,0--inactive **/
	private Integer status;

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

    private List<ImgUrl> imgs;

    public List<ImgUrl> getImgs() {
        return imgs;
    }

    public void setImgs(List<ImgUrl> imgs) {
        this.imgs = imgs;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getFoodCode() {
        return foodCode;
    }

    public void setFoodCode(String foodCode) {
        this.foodCode = foodCode;
    }


	public String getImgId() {
        return imgId;
    }

    public void setImgId(String imgId) {
        this.imgId = imgId;
    }


	public String getFoodName() {
        return foodName;
    }

    public void setFoodName(String foodName) {
        this.foodName = foodName;
    }


	public String getFoodUnit() {
        return foodUnit;
    }

    public void setFoodUnit(String foodUnit) {
        this.foodUnit = foodUnit;
    }


	public BigDecimal getFoodPrice() {
        return foodPrice;
    }

    public void setFoodPrice(BigDecimal foodPrice) {
        this.foodPrice = foodPrice;
    }


	public String getFoodPack() {
        return foodPack;
    }

    public void setFoodPack(String foodPack) {
        this.foodPack = foodPack;
    }


	public String getFoodSpec() {
        return foodSpec;
    }

    public void setFoodSpec(String foodSpec) {
        this.foodSpec = foodSpec;
    }


	public Integer getFoodStatus() {
        return foodStatus;
    }

    public void setFoodStatus(Integer foodStatus) {
        this.foodStatus = foodStatus;
    }

    public String getFoodDesc() {
		return foodDesc;
	}

	public void setFoodDesc(String foodDesc) {
		this.foodDesc = foodDesc;
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


	public Integer getStatus() {
        return status;
    }

    public void setStatus(Integer status) {
        this.status = status;
    }


	public BaseFood() {
        super();
    }


	public BaseFood(String id,String foodCode,String imgId,String foodName,String foodUnit,BigDecimal foodPrice,String foodPack,String foodSpec,Integer foodStatus,String createBy,Date createDate,String updateBy,Date updateDate,Integer status,List<ImgUrl> imgs) {

		this.id = id;
		this.foodCode = foodCode;
		this.imgId = imgId;
		this.foodName = foodName;
		this.foodUnit = foodUnit;
		this.foodPrice = foodPrice;
		this.foodPack = foodPack;
		this.foodSpec = foodSpec;
		this.foodStatus = foodStatus;
		this.createBy = createBy;
		this.createDate = createDate;
		this.updateBy = updateBy;
		this.updateDate = updateDate;
		this.status = status;
		this.imgs = imgs;

	}

}
