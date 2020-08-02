package com.cpdms.model.dinning;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 每日菜品 DailyDishesExample
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:15:11
 */
public class DailyDishesExample {

    protected String orderByClause;

    protected boolean distinct;

    protected List<Criteria> oredCriteria;

    public DailyDishesExample() {
        oredCriteria = new ArrayList<Criteria>();
    }

    public void setOrderByClause(String orderByClause) {
        this.orderByClause = orderByClause;
    }

    public String getOrderByClause() {
        return orderByClause;
    }

    public void setDistinct(boolean distinct) {
        this.distinct = distinct;
    }

    public boolean isDistinct() {
        return distinct;
    }

    public List<Criteria> getOredCriteria() {
        return oredCriteria;
    }

    public void or(Criteria criteria) {
        oredCriteria.add(criteria);
    }

    public Criteria or() {
        Criteria criteria = createCriteriaInternal();
        oredCriteria.add(criteria);
        return criteria;
    }

    public Criteria createCriteria() {
        Criteria criteria = createCriteriaInternal();
        if (oredCriteria.size() == 0) {
            oredCriteria.add(criteria);
        }
        return criteria;
    }

    protected Criteria createCriteriaInternal() {
        Criteria criteria = new Criteria();
        return criteria;
    }

    public void clear() {
        oredCriteria.clear();
        orderByClause = null;
        distinct = false;
    }

    protected abstract static class GeneratedCriteria {
        protected List<Criterion> criteria;

        protected GeneratedCriteria() {
            super();
            criteria = new ArrayList<Criterion>();
        }

        public boolean isValid() {
            return criteria.size() > 0;
        }

        public List<Criterion> getAllCriteria() {
            return criteria;
        }

        public List<Criterion> getCriteria() {
            return criteria;
        }

        protected void addCriterion(String condition) {
            if (condition == null) {
                throw new RuntimeException("Value for condition cannot be null");
            }
            criteria.add(new Criterion(condition));
        }

        protected void addCriterion(String condition, Object value, String property) {
            if (value == null) {
                throw new RuntimeException("Value for " + property + " cannot be null");
            }
            criteria.add(new Criterion(condition, value));
        }

        protected void addCriterion(String condition, Object value1, Object value2, String property) {
            if (value1 == null || value2 == null) {
                throw new RuntimeException("Between values for " + property + " cannot be null");
            }
            criteria.add(new Criterion(condition, value1, value2));
        }
				
        public Criteria andIdIsNull() {
            addCriterion("id is null");
            return (Criteria) this;
        }

        public Criteria andIdIsNotNull() {
            addCriterion("id is not null");
            return (Criteria) this;
        }

        public Criteria andIdEqualTo(String value) {
            addCriterion("id =", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotEqualTo(String value) {
            addCriterion("id <>", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdGreaterThan(String value) {
            addCriterion("id >", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdGreaterThanOrEqualTo(String value) {
            addCriterion("id >=", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLessThan(String value) {
            addCriterion("id <", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLessThanOrEqualTo(String value) {
            addCriterion("id <=", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLike(String value) {
            addCriterion("id like", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotLike(String value) {
            addCriterion("id not like", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdIn(List<String> values) {
            addCriterion("id in", values, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotIn(List<String> values) {
            addCriterion("id not in", values, "id");
            return (Criteria) this;
        }

        public Criteria andIdBetween(String value1, String value2) {
            addCriterion("id between", value1, value2, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotBetween(String value1, String value2) {
            addCriterion("id not between", value1, value2, "id");
            return (Criteria) this;
        }
        
			
        public Criteria andGroupIdIsNull() {
            addCriterion("group_id is null");
            return (Criteria) this;
        }

        public Criteria andGroupIdIsNotNull() {
            addCriterion("group_id is not null");
            return (Criteria) this;
        }

        public Criteria andGroupIdEqualTo(String value) {
            addCriterion("group_id =", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdNotEqualTo(String value) {
            addCriterion("group_id <>", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdGreaterThan(String value) {
            addCriterion("group_id >", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdGreaterThanOrEqualTo(String value) {
            addCriterion("group_id >=", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdLessThan(String value) {
            addCriterion("group_id <", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdLessThanOrEqualTo(String value) {
            addCriterion("group_id <=", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdLike(String value) {
            addCriterion("group_id like", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdNotLike(String value) {
            addCriterion("group_id not like", value, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdIn(List<String> values) {
            addCriterion("group_id in", values, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdNotIn(List<String> values) {
            addCriterion("group_id not in", values, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdBetween(String value1, String value2) {
            addCriterion("group_id between", value1, value2, "groupId");
            return (Criteria) this;
        }

        public Criteria andGroupIdNotBetween(String value1, String value2) {
            addCriterion("group_id not between", value1, value2, "groupId");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodIdIsNull() {
            addCriterion("food_id is null");
            return (Criteria) this;
        }

        public Criteria andFoodIdIsNotNull() {
            addCriterion("food_id is not null");
            return (Criteria) this;
        }

        public Criteria andFoodIdEqualTo(String value) {
            addCriterion("food_id =", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdNotEqualTo(String value) {
            addCriterion("food_id <>", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdGreaterThan(String value) {
            addCriterion("food_id >", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdGreaterThanOrEqualTo(String value) {
            addCriterion("food_id >=", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdLessThan(String value) {
            addCriterion("food_id <", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdLessThanOrEqualTo(String value) {
            addCriterion("food_id <=", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdLike(String value) {
            addCriterion("food_id like", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdNotLike(String value) {
            addCriterion("food_id not like", value, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdIn(List<String> values) {
            addCriterion("food_id in", values, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdNotIn(List<String> values) {
            addCriterion("food_id not in", values, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdBetween(String value1, String value2) {
            addCriterion("food_id between", value1, value2, "foodId");
            return (Criteria) this;
        }

        public Criteria andFoodIdNotBetween(String value1, String value2) {
            addCriterion("food_id not between", value1, value2, "foodId");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodNameIsNull() {
            addCriterion("food_name is null");
            return (Criteria) this;
        }

        public Criteria andFoodNameIsNotNull() {
            addCriterion("food_name is not null");
            return (Criteria) this;
        }

        public Criteria andFoodNameEqualTo(String value) {
            addCriterion("food_name =", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotEqualTo(String value) {
            addCriterion("food_name <>", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameGreaterThan(String value) {
            addCriterion("food_name >", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameGreaterThanOrEqualTo(String value) {
            addCriterion("food_name >=", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLessThan(String value) {
            addCriterion("food_name <", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLessThanOrEqualTo(String value) {
            addCriterion("food_name <=", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLike(String value) {
            addCriterion("food_name like", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotLike(String value) {
            addCriterion("food_name not like", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameIn(List<String> values) {
            addCriterion("food_name in", values, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotIn(List<String> values) {
            addCriterion("food_name not in", values, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameBetween(String value1, String value2) {
            addCriterion("food_name between", value1, value2, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotBetween(String value1, String value2) {
            addCriterion("food_name not between", value1, value2, "foodName");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodPriceIsNull() {
            addCriterion("food_price is null");
            return (Criteria) this;
        }

        public Criteria andFoodPriceIsNotNull() {
            addCriterion("food_price is not null");
            return (Criteria) this;
        }

        public Criteria andFoodPriceEqualTo(BigDecimal value) {
            addCriterion("food_price =", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotEqualTo(BigDecimal value) {
            addCriterion("food_price <>", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceGreaterThan(BigDecimal value) {
            addCriterion("food_price >", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceGreaterThanOrEqualTo(BigDecimal value) {
            addCriterion("food_price >=", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLessThan(BigDecimal value) {
            addCriterion("food_price <", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLessThanOrEqualTo(BigDecimal value) {
            addCriterion("food_price <=", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLike(BigDecimal value) {
            addCriterion("food_price like", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotLike(BigDecimal value) {
            addCriterion("food_price not like", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceIn(List<BigDecimal> values) {
            addCriterion("food_price in", values, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotIn(List<BigDecimal> values) {
            addCriterion("food_price not in", values, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("food_price between", value1, value2, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("food_price not between", value1, value2, "foodPrice");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodStatusIsNull() {
            addCriterion("food_status is null");
            return (Criteria) this;
        }

        public Criteria andFoodStatusIsNotNull() {
            addCriterion("food_status is not null");
            return (Criteria) this;
        }

        public Criteria andFoodStatusEqualTo(Integer value) {
            addCriterion("food_status =", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotEqualTo(Integer value) {
            addCriterion("food_status <>", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusGreaterThan(Integer value) {
            addCriterion("food_status >", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusGreaterThanOrEqualTo(Integer value) {
            addCriterion("food_status >=", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLessThan(Integer value) {
            addCriterion("food_status <", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLessThanOrEqualTo(Integer value) {
            addCriterion("food_status <=", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLike(Integer value) {
            addCriterion("food_status like", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotLike(Integer value) {
            addCriterion("food_status not like", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusIn(List<Integer> values) {
            addCriterion("food_status in", values, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotIn(List<Integer> values) {
            addCriterion("food_status not in", values, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusBetween(Integer value1, Integer value2) {
            addCriterion("food_status between", value1, value2, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotBetween(Integer value1, Integer value2) {
            addCriterion("food_status not between", value1, value2, "foodStatus");
            return (Criteria) this;
        }
        
			
        public Criteria andSellTypeNameIsNull() {
            addCriterion("sell_type_name is null");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameIsNotNull() {
            addCriterion("sell_type_name is not null");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameEqualTo(String value) {
            addCriterion("sell_type_name =", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameNotEqualTo(String value) {
            addCriterion("sell_type_name <>", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameGreaterThan(String value) {
            addCriterion("sell_type_name >", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameGreaterThanOrEqualTo(String value) {
            addCriterion("sell_type_name >=", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameLessThan(String value) {
            addCriterion("sell_type_name <", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameLessThanOrEqualTo(String value) {
            addCriterion("sell_type_name <=", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameLike(String value) {
            addCriterion("sell_type_name like", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameNotLike(String value) {
            addCriterion("sell_type_name not like", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameIn(List<String> values) {
            addCriterion("sell_type_name in", values, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameNotIn(List<String> values) {
            addCriterion("sell_type_name not in", values, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameBetween(String value1, String value2) {
            addCriterion("sell_type_name between", value1, value2, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameNotBetween(String value1, String value2) {
            addCriterion("sell_type_name not between", value1, value2, "sellTypeName");
            return (Criteria) this;
        }
        
			
        public Criteria andSellTypeIdsIsNull() {
            addCriterion("sell_type_ids is null");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsIsNotNull() {
            addCriterion("sell_type_ids is not null");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsEqualTo(String value) {
            addCriterion("sell_type_ids =", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsNotEqualTo(String value) {
            addCriterion("sell_type_ids <>", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsGreaterThan(String value) {
            addCriterion("sell_type_ids >", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsGreaterThanOrEqualTo(String value) {
            addCriterion("sell_type_ids >=", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsLessThan(String value) {
            addCriterion("sell_type_ids <", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsLessThanOrEqualTo(String value) {
            addCriterion("sell_type_ids <=", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsLike(String value) {
            addCriterion("sell_type_ids like", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsNotLike(String value) {
            addCriterion("sell_type_ids not like", value, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsIn(List<String> values) {
            addCriterion("sell_type_ids in", values, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsNotIn(List<String> values) {
            addCriterion("sell_type_ids not in", values, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsBetween(String value1, String value2) {
            addCriterion("sell_type_ids between", value1, value2, "sellTypeIds");
            return (Criteria) this;
        }

        public Criteria andSellTypeIdsNotBetween(String value1, String value2) {
            addCriterion("sell_type_ids not between", value1, value2, "sellTypeIds");
            return (Criteria) this;
        }
        
			
        public Criteria andRangeNameIsNull() {
            addCriterion("range_name is null");
            return (Criteria) this;
        }

        public Criteria andRangeNameIsNotNull() {
            addCriterion("range_name is not null");
            return (Criteria) this;
        }

        public Criteria andRangeNameEqualTo(String value) {
            addCriterion("range_name =", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameNotEqualTo(String value) {
            addCriterion("range_name <>", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameGreaterThan(String value) {
            addCriterion("range_name >", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameGreaterThanOrEqualTo(String value) {
            addCriterion("range_name >=", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameLessThan(String value) {
            addCriterion("range_name <", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameLessThanOrEqualTo(String value) {
            addCriterion("range_name <=", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameLike(String value) {
            addCriterion("range_name like", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameNotLike(String value) {
            addCriterion("range_name not like", value, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameIn(List<String> values) {
            addCriterion("range_name in", values, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameNotIn(List<String> values) {
            addCriterion("range_name not in", values, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameBetween(String value1, String value2) {
            addCriterion("range_name between", value1, value2, "rangeName");
            return (Criteria) this;
        }

        public Criteria andRangeNameNotBetween(String value1, String value2) {
            addCriterion("range_name not between", value1, value2, "rangeName");
            return (Criteria) this;
        }
        
			
        public Criteria andRangeIdsIsNull() {
            addCriterion("range_ids is null");
            return (Criteria) this;
        }

        public Criteria andRangeIdsIsNotNull() {
            addCriterion("range_ids is not null");
            return (Criteria) this;
        }

        public Criteria andRangeIdsEqualTo(String value) {
            addCriterion("range_ids =", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsNotEqualTo(String value) {
            addCriterion("range_ids <>", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsGreaterThan(String value) {
            addCriterion("range_ids >", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsGreaterThanOrEqualTo(String value) {
            addCriterion("range_ids >=", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsLessThan(String value) {
            addCriterion("range_ids <", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsLessThanOrEqualTo(String value) {
            addCriterion("range_ids <=", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsLike(String value) {
            addCriterion("range_ids like", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsNotLike(String value) {
            addCriterion("range_ids not like", value, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsIn(List<String> values) {
            addCriterion("range_ids in", values, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsNotIn(List<String> values) {
            addCriterion("range_ids not in", values, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsBetween(String value1, String value2) {
            addCriterion("range_ids between", value1, value2, "rangeIds");
            return (Criteria) this;
        }

        public Criteria andRangeIdsNotBetween(String value1, String value2) {
            addCriterion("range_ids not between", value1, value2, "rangeIds");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodBeginTimeIsNull() {
            addCriterion("food_begin_time is null");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeIsNotNull() {
            addCriterion("food_begin_time is not null");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeEqualTo(Date value) {
            addCriterion("food_begin_time =", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeNotEqualTo(Date value) {
            addCriterion("food_begin_time <>", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeGreaterThan(Date value) {
            addCriterion("food_begin_time >", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeGreaterThanOrEqualTo(Date value) {
            addCriterion("food_begin_time >=", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeLessThan(Date value) {
            addCriterion("food_begin_time <", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeLessThanOrEqualTo(Date value) {
            addCriterion("food_begin_time <=", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeLike(Date value) {
            addCriterion("food_begin_time like", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeNotLike(Date value) {
            addCriterion("food_begin_time not like", value, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeIn(List<Date> values) {
            addCriterion("food_begin_time in", values, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeNotIn(List<Date> values) {
            addCriterion("food_begin_time not in", values, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeBetween(Date value1, Date value2) {
            addCriterion("food_begin_time between", value1, value2, "foodBeginTime");
            return (Criteria) this;
        }

        public Criteria andFoodBeginTimeNotBetween(Date value1, Date value2) {
            addCriterion("food_begin_time not between", value1, value2, "foodBeginTime");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodEndTimeIsNull() {
            addCriterion("food_end_time is null");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeIsNotNull() {
            addCriterion("food_end_time is not null");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeEqualTo(Date value) {
            addCriterion("food_end_time =", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeNotEqualTo(Date value) {
            addCriterion("food_end_time <>", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeGreaterThan(Date value) {
            addCriterion("food_end_time >", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeGreaterThanOrEqualTo(Date value) {
            addCriterion("food_end_time >=", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeLessThan(Date value) {
            addCriterion("food_end_time <", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeLessThanOrEqualTo(Date value) {
            addCriterion("food_end_time <=", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeLike(Date value) {
            addCriterion("food_end_time like", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeNotLike(Date value) {
            addCriterion("food_end_time not like", value, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeIn(List<Date> values) {
            addCriterion("food_end_time in", values, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeNotIn(List<Date> values) {
            addCriterion("food_end_time not in", values, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeBetween(Date value1, Date value2) {
            addCriterion("food_end_time between", value1, value2, "foodEndTime");
            return (Criteria) this;
        }

        public Criteria andFoodEndTimeNotBetween(Date value1, Date value2) {
            addCriterion("food_end_time not between", value1, value2, "foodEndTime");
            return (Criteria) this;
        }
        
			
        public Criteria andCreateDateIsNull() {
            addCriterion("create_date is null");
            return (Criteria) this;
        }

        public Criteria andCreateDateIsNotNull() {
            addCriterion("create_date is not null");
            return (Criteria) this;
        }

        public Criteria andCreateDateEqualTo(Date value) {
            addCriterion("create_date =", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotEqualTo(Date value) {
            addCriterion("create_date <>", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateGreaterThan(Date value) {
            addCriterion("create_date >", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateGreaterThanOrEqualTo(Date value) {
            addCriterion("create_date >=", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLessThan(Date value) {
            addCriterion("create_date <", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLessThanOrEqualTo(Date value) {
            addCriterion("create_date <=", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLike(Date value) {
            addCriterion("create_date like", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotLike(Date value) {
            addCriterion("create_date not like", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateIn(List<Date> values) {
            addCriterion("create_date in", values, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotIn(List<Date> values) {
            addCriterion("create_date not in", values, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateBetween(Date value1, Date value2) {
            addCriterion("create_date between", value1, value2, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotBetween(Date value1, Date value2) {
            addCriterion("create_date not between", value1, value2, "createDate");
            return (Criteria) this;
        }
        
			
        public Criteria andUpdateDateIsNull() {
            addCriterion("update_date is null");
            return (Criteria) this;
        }

        public Criteria andUpdateDateIsNotNull() {
            addCriterion("update_date is not null");
            return (Criteria) this;
        }

        public Criteria andUpdateDateEqualTo(Date value) {
            addCriterion("update_date =", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotEqualTo(Date value) {
            addCriterion("update_date <>", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateGreaterThan(Date value) {
            addCriterion("update_date >", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateGreaterThanOrEqualTo(Date value) {
            addCriterion("update_date >=", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLessThan(Date value) {
            addCriterion("update_date <", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLessThanOrEqualTo(Date value) {
            addCriterion("update_date <=", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLike(Date value) {
            addCriterion("update_date like", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotLike(Date value) {
            addCriterion("update_date not like", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateIn(List<Date> values) {
            addCriterion("update_date in", values, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotIn(List<Date> values) {
            addCriterion("update_date not in", values, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateBetween(Date value1, Date value2) {
            addCriterion("update_date between", value1, value2, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotBetween(Date value1, Date value2) {
            addCriterion("update_date not between", value1, value2, "updateDate");
            return (Criteria) this;
        }
        
			
        public Criteria andCreateByIsNull() {
            addCriterion("create_by is null");
            return (Criteria) this;
        }

        public Criteria andCreateByIsNotNull() {
            addCriterion("create_by is not null");
            return (Criteria) this;
        }

        public Criteria andCreateByEqualTo(String value) {
            addCriterion("create_by =", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotEqualTo(String value) {
            addCriterion("create_by <>", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByGreaterThan(String value) {
            addCriterion("create_by >", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByGreaterThanOrEqualTo(String value) {
            addCriterion("create_by >=", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLessThan(String value) {
            addCriterion("create_by <", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLessThanOrEqualTo(String value) {
            addCriterion("create_by <=", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLike(String value) {
            addCriterion("create_by like", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotLike(String value) {
            addCriterion("create_by not like", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByIn(List<String> values) {
            addCriterion("create_by in", values, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotIn(List<String> values) {
            addCriterion("create_by not in", values, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByBetween(String value1, String value2) {
            addCriterion("create_by between", value1, value2, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotBetween(String value1, String value2) {
            addCriterion("create_by not between", value1, value2, "createBy");
            return (Criteria) this;
        }
        
			
        public Criteria andUpdateByIsNull() {
            addCriterion("update_by is null");
            return (Criteria) this;
        }

        public Criteria andUpdateByIsNotNull() {
            addCriterion("update_by is not null");
            return (Criteria) this;
        }

        public Criteria andUpdateByEqualTo(String value) {
            addCriterion("update_by =", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotEqualTo(String value) {
            addCriterion("update_by <>", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByGreaterThan(String value) {
            addCriterion("update_by >", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByGreaterThanOrEqualTo(String value) {
            addCriterion("update_by >=", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLessThan(String value) {
            addCriterion("update_by <", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLessThanOrEqualTo(String value) {
            addCriterion("update_by <=", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLike(String value) {
            addCriterion("update_by like", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotLike(String value) {
            addCriterion("update_by not like", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByIn(List<String> values) {
            addCriterion("update_by in", values, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotIn(List<String> values) {
            addCriterion("update_by not in", values, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByBetween(String value1, String value2) {
            addCriterion("update_by between", value1, value2, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotBetween(String value1, String value2) {
            addCriterion("update_by not between", value1, value2, "updateBy");
            return (Criteria) this;
        }
        
	}

    public static class Criteria extends GeneratedCriteria {

        protected Criteria() {
            super();
        }
    }

    public static class Criterion {
        private String condition;

        private Object value;

        private Object secondValue;

        private boolean noValue;

        private boolean singleValue;

        private boolean betweenValue;

        private boolean listValue;

        private String typeHandler;

        public String getCondition() {
            return condition;
        }

        public Object getValue() {
            return value;
        }

        public Object getSecondValue() {
            return secondValue;
        }

        public boolean isNoValue() {
            return noValue;
        }

        public boolean isSingleValue() {
            return singleValue;
        }

        public boolean isBetweenValue() {
            return betweenValue;
        }

        public boolean isListValue() {
            return listValue;
        }

        public String getTypeHandler() {
            return typeHandler;
        }

        protected Criterion(String condition) {
            super();
            this.condition = condition;
            this.typeHandler = null;
            this.noValue = true;
        }

        protected Criterion(String condition, Object value, String typeHandler) {
            super();
            this.condition = condition;
            this.value = value;
            this.typeHandler = typeHandler;
            if (value instanceof List<?>) {
                this.listValue = true;
            } else {
                this.singleValue = true;
            }
        }

        protected Criterion(String condition, Object value) {
            this(condition, value, null);
        }

        protected Criterion(String condition, Object value, Object secondValue, String typeHandler) {
            super();
            this.condition = condition;
            this.value = value;
            this.secondValue = secondValue;
            this.typeHandler = typeHandler;
            this.betweenValue = true;
        }

        protected Criterion(String condition, Object value, Object secondValue) {
            this(condition, value, secondValue, null);
        }
    }
}