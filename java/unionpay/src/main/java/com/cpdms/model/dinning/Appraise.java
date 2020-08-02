package com.cpdms.model.dinning;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.Max;
import javax.validation.constraints.Min;
import javax.validation.constraints.NotBlank;
import java.io.Serializable;
import java.lang.Double;
import java.util.Date;

/**
 * 评价表 Appraise
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 15:47:57
 */
public class Appraise implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 菜品id **/
	@NotBlank(message = "菜品不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String foodId;

	/** 分数 **/
	@Min(value = 0, message = "最小0分")
    @Max(value = 100, message = "最大100分")
	private Double fraction;

	/** 星级 **/
	@Min(value = 0, message = "最小0星")
    @Max(value = 5, message = "最大5星")
	private Double star;

	/*姓名*/
    @NotBlank(message = "姓名不能为空")
    @JsonSerialize(using= StringFormat.class)
    private String empName;

    @JsonSerialize(using= StringFormat.class)
    private String aDeptId;

    public String getaDeptId() {
        return aDeptId;
    }

    public void setaDeptId(String aDeptId) {
        this.aDeptId = aDeptId;
    }

    /*卡号*/
    @NotBlank(message = "菜品不能为空")
    @JsonSerialize(using= StringFormat.class)
    private String CardNo;

    @JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss",timezone="GMT+8")
    private Date createTime;

    @JsonSerialize(using= StringFormat.class)
    private String source;
    @JsonSerialize(using= StringFormat.class)
    private String ordId;

    public String getSource() {
        return source;
    }

    public void setSource(String source) {
        this.source = source;
    }

    public String getOrdId() {
        return ordId;
    }

    public void setOrdId(String ordId) {
        this.ordId = ordId;
    }

    public Date getCreateTime() {
        return createTime;
    }

    public void setCreateTime(Date createTime) {
        this.createTime = createTime;
    }

    public String getEmpName() {
        return empName;
    }

    public void setEmpName(String empName) {
        this.empName = empName;
    }

    public String getCardNo() {
        return CardNo;
    }

    public void setCardNo(String cardNo) {
        CardNo = cardNo;
    }

    /*菜品名称*/
    @JsonSerialize(using= StringFormat.class)
	private String foodName;
	/*菜品图片*/
    @JsonSerialize(using= StringFormat.class)
	private String imgPath;

    public String getImgPath() {
        return imgPath;
    }

    public void setImgPath(String imgPath) {
        this.imgPath = imgPath;
    }

    public String getFoodName() {
        return foodName;
    }

    public void setFoodName(String foodName) {
        this.foodName = foodName;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }


	public Double getFraction() {
        return fraction;
    }

    public void setFraction(Double fraction) {
        this.fraction = fraction;
    }


	public Double getStar() {
        return star;
    }

    public void setStar(Double star) {
        this.star = star;
    }


	public Appraise() {
        super();
    }


	public Appraise(String id,String foodId,Double fraction,Double star) {

		this.id = id;
		this.foodId = foodId;
		this.fraction = fraction;
		this.star = star;

	}

}
