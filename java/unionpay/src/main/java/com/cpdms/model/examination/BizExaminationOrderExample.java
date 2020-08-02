package com.cpdms.model.examination;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 体检预约表 BizExaminationOrderExample
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:53:05
 */
public class BizExaminationOrderExample {

    protected String orderByClause;

    protected boolean distinct;

    protected List<Criteria> oredCriteria;

    public BizExaminationOrderExample() {
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


        public Criteria andOrdCodeIsNull() {
            addCriterion("ord_code is null");
            return (Criteria) this;
        }

        public Criteria andOrdCodeIsNotNull() {
            addCriterion("ord_code is not null");
            return (Criteria) this;
        }

        public Criteria andOrdCodeEqualTo(String value) {
            addCriterion("ord_code =", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotEqualTo(String value) {
            addCriterion("ord_code <>", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeGreaterThan(String value) {
            addCriterion("ord_code >", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeGreaterThanOrEqualTo(String value) {
            addCriterion("ord_code >=", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLessThan(String value) {
            addCriterion("ord_code <", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLessThanOrEqualTo(String value) {
            addCriterion("ord_code <=", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLike(String value) {
            addCriterion("ord_code like", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotLike(String value) {
            addCriterion("ord_code not like", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeIn(List<String> values) {
            addCriterion("ord_code in", values, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotIn(List<String> values) {
            addCriterion("ord_code not in", values, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeBetween(String value1, String value2) {
            addCriterion("ord_code between", value1, value2, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotBetween(String value1, String value2) {
            addCriterion("ord_code not between", value1, value2, "ordCode");
            return (Criteria) this;
        }


        public Criteria andOrdTimeIsNull() {
            addCriterion("ord_time is null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeIsNotNull() {
            addCriterion("ord_time is not null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeEqualTo(String value) {
            addCriterion("ord_time =", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotEqualTo(String value) {
            addCriterion("ord_time <>", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeGreaterThan(String value) {
            addCriterion("ord_time >", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeGreaterThanOrEqualTo(String value) {
            addCriterion("ord_time >=", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLessThan(String value) {
            addCriterion("ord_time <", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLessThanOrEqualTo(String value) {
            addCriterion("ord_time <=", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLike(String value) {
            addCriterion("ord_time like", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotLike(String value) {
            addCriterion("ord_time not like", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeIn(List<String> values) {
            addCriterion("ord_time in", values, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotIn(List<String> values) {
            addCriterion("ord_time not in", values, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeBetween(String value1, String value2) {
            addCriterion("ord_time between", value1, value2, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotBetween(String value1, String value2) {
            addCriterion("ord_time not between", value1, value2, "ordTime");
            return (Criteria) this;
        }


        public Criteria andFinishTimeIsNull() {
            addCriterion("finish_time is null");
            return (Criteria) this;
        }

        public Criteria andFinishTimeIsNotNull() {
            addCriterion("finish_time is not null");
            return (Criteria) this;
        }

        public Criteria andFinishTimeEqualTo(String value) {
            addCriterion("finish_time =", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeNotEqualTo(String value) {
            addCriterion("finish_time <>", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeGreaterThan(String value) {
            addCriterion("finish_time >", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeGreaterThanOrEqualTo(String value) {
            addCriterion("finish_time >=", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeLessThan(String value) {
            addCriterion("finish_time <", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeLessThanOrEqualTo(String value) {
            addCriterion("finish_time <=", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeLike(String value) {
            addCriterion("finish_time like", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeNotLike(String value) {
            addCriterion("finish_time not like", value, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeIn(List<String> values) {
            addCriterion("finish_time in", values, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeNotIn(List<String> values) {
            addCriterion("finish_time not in", values, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeBetween(String value1, String value2) {
            addCriterion("finish_time between", value1, value2, "finishTime");
            return (Criteria) this;
        }

        public Criteria andFinishTimeNotBetween(String value1, String value2) {
            addCriterion("finish_time not between", value1, value2, "finishTime");
            return (Criteria) this;
        }


        public Criteria andOrdEmpIdIsNull() {
            addCriterion("ord_emp_id is null");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdIsNotNull() {
            addCriterion("ord_emp_id is not null");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdEqualTo(String value) {
            addCriterion("ord_emp_id =", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotEqualTo(String value) {
            addCriterion("ord_emp_id <>", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdGreaterThan(String value) {
            addCriterion("ord_emp_id >", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdGreaterThanOrEqualTo(String value) {
            addCriterion("ord_emp_id >=", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLessThan(String value) {
            addCriterion("ord_emp_id <", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLessThanOrEqualTo(String value) {
            addCriterion("ord_emp_id <=", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLike(String value) {
            addCriterion("ord_emp_id like", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotLike(String value) {
            addCriterion("ord_emp_id not like", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdIn(List<String> values) {
            addCriterion("ord_emp_id in", values, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotIn(List<String> values) {
            addCriterion("ord_emp_id not in", values, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdBetween(String value1, String value2) {
            addCriterion("ord_emp_id between", value1, value2, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotBetween(String value1, String value2) {
            addCriterion("ord_emp_id not between", value1, value2, "ordEmpId");
            return (Criteria) this;
        }


        public Criteria andOrdStateIsNull() {
            addCriterion("ord_state is null");
            return (Criteria) this;
        }

        public Criteria andOrdStateIsNotNull() {
            addCriterion("ord_state is not null");
            return (Criteria) this;
        }

        public Criteria andOrdStateEqualTo(String value) {
            addCriterion("ord_state =", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotEqualTo(String value) {
            addCriterion("ord_state <>", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateGreaterThan(String value) {
            addCriterion("ord_state >", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateGreaterThanOrEqualTo(String value) {
            addCriterion("ord_state >=", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLessThan(String value) {
            addCriterion("ord_state <", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLessThanOrEqualTo(String value) {
            addCriterion("ord_state <=", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLike(String value) {
            addCriterion("ord_state like", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotLike(String value) {
            addCriterion("ord_state not like", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateIn(List<String> values) {
            addCriterion("ord_state in", values, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotIn(List<String> values) {
            addCriterion("ord_state not in", values, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateBetween(String value1, String value2) {
            addCriterion("ord_state between", value1, value2, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotBetween(String value1, String value2) {
            addCriterion("ord_state not between", value1, value2, "ordState");
            return (Criteria) this;
        }


        public Criteria andOrdSrcIsNull() {
            addCriterion("ord_src is null");
            return (Criteria) this;
        }

        public Criteria andOrdSrcIsNotNull() {
            addCriterion("ord_src is not null");
            return (Criteria) this;
        }

        public Criteria andOrdSrcEqualTo(String value) {
            addCriterion("ord_src =", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotEqualTo(String value) {
            addCriterion("ord_src <>", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcGreaterThan(String value) {
            addCriterion("ord_src >", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcGreaterThanOrEqualTo(String value) {
            addCriterion("ord_src >=", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLessThan(String value) {
            addCriterion("ord_src <", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLessThanOrEqualTo(String value) {
            addCriterion("ord_src <=", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLike(String value) {
            addCriterion("ord_src like", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotLike(String value) {
            addCriterion("ord_src not like", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcIn(List<String> values) {
            addCriterion("ord_src in", values, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotIn(List<String> values) {
            addCriterion("ord_src not in", values, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcBetween(String value1, String value2) {
            addCriterion("ord_src between", value1, value2, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotBetween(String value1, String value2) {
            addCriterion("ord_src not between", value1, value2, "ordSrc");
            return (Criteria) this;
        }


        public Criteria andNameIsNull() {
            addCriterion("name is null");
            return (Criteria) this;
        }

        public Criteria andNameIsNotNull() {
            addCriterion("name is not null");
            return (Criteria) this;
        }

        public Criteria andNameEqualTo(String value) {
            addCriterion("name =", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameNotEqualTo(String value) {
            addCriterion("name <>", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameGreaterThan(String value) {
            addCriterion("name >", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameGreaterThanOrEqualTo(String value) {
            addCriterion("name >=", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameLessThan(String value) {
            addCriterion("name <", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameLessThanOrEqualTo(String value) {
            addCriterion("name <=", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameLike(String value) {
            addCriterion("name like", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameNotLike(String value) {
            addCriterion("name not like", value, "name");
            return (Criteria) this;
        }

        public Criteria andNameIn(List<String> values) {
            addCriterion("name in", values, "name");
            return (Criteria) this;
        }

        public Criteria andNameNotIn(List<String> values) {
            addCriterion("name not in", values, "name");
            return (Criteria) this;
        }

        public Criteria andNameBetween(String value1, String value2) {
            addCriterion("name between", value1, value2, "name");
            return (Criteria) this;
        }

        public Criteria andNameNotBetween(String value1, String value2) {
            addCriterion("name not between", value1, value2, "name");
            return (Criteria) this;
        }


        public Criteria andTelIsNull() {
            addCriterion("tel is null");
            return (Criteria) this;
        }

        public Criteria andTelIsNotNull() {
            addCriterion("tel is not null");
            return (Criteria) this;
        }

        public Criteria andTelEqualTo(String value) {
            addCriterion("tel =", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotEqualTo(String value) {
            addCriterion("tel <>", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelGreaterThan(String value) {
            addCriterion("tel >", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelGreaterThanOrEqualTo(String value) {
            addCriterion("tel >=", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLessThan(String value) {
            addCriterion("tel <", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLessThanOrEqualTo(String value) {
            addCriterion("tel <=", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLike(String value) {
            addCriterion("tel like", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotLike(String value) {
            addCriterion("tel not like", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelIn(List<String> values) {
            addCriterion("tel in", values, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotIn(List<String> values) {
            addCriterion("tel not in", values, "tel");
            return (Criteria) this;
        }

        public Criteria andTelBetween(String value1, String value2) {
            addCriterion("tel between", value1, value2, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotBetween(String value1, String value2) {
            addCriterion("tel not between", value1, value2, "tel");
            return (Criteria) this;
        }


        public Criteria andCardNoIsNull() {
            addCriterion("card_no is null");
            return (Criteria) this;
        }

        public Criteria andCardNoIsNotNull() {
            addCriterion("card_no is not null");
            return (Criteria) this;
        }

        public Criteria andCardNoEqualTo(String value) {
            addCriterion("card_no =", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoNotEqualTo(String value) {
            addCriterion("card_no <>", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoGreaterThan(String value) {
            addCriterion("card_no >", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoGreaterThanOrEqualTo(String value) {
            addCriterion("card_no >=", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoLessThan(String value) {
            addCriterion("card_no <", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoLessThanOrEqualTo(String value) {
            addCriterion("card_no <=", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoLike(String value) {
            addCriterion("card_no like", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoNotLike(String value) {
            addCriterion("card_no not like", value, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoIn(List<String> values) {
            addCriterion("card_no in", values, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoNotIn(List<String> values) {
            addCriterion("card_no not in", values, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoBetween(String value1, String value2) {
            addCriterion("card_no between", value1, value2, "cardNo");
            return (Criteria) this;
        }

        public Criteria andCardNoNotBetween(String value1, String value2) {
            addCriterion("card_no not between", value1, value2, "cardNo");
            return (Criteria) this;
        }


        public Criteria andCertIdIsNull() {
            addCriterion("cert_id is null");
            return (Criteria) this;
        }

        public Criteria andCertIdIsNotNull() {
            addCriterion("cert_id is not null");
            return (Criteria) this;
        }

        public Criteria andCertIdEqualTo(String value) {
            addCriterion("cert_id =", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdNotEqualTo(String value) {
            addCriterion("cert_id <>", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdGreaterThan(String value) {
            addCriterion("cert_id >", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdGreaterThanOrEqualTo(String value) {
            addCriterion("cert_id >=", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdLessThan(String value) {
            addCriterion("cert_id <", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdLessThanOrEqualTo(String value) {
            addCriterion("cert_id <=", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdLike(String value) {
            addCriterion("cert_id like", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdNotLike(String value) {
            addCriterion("cert_id not like", value, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdIn(List<String> values) {
            addCriterion("cert_id in", values, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdNotIn(List<String> values) {
            addCriterion("cert_id not in", values, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdBetween(String value1, String value2) {
            addCriterion("cert_id between", value1, value2, "certId");
            return (Criteria) this;
        }

        public Criteria andCertIdNotBetween(String value1, String value2) {
            addCriterion("cert_id not between", value1, value2, "certId");
            return (Criteria) this;
        }


        public Criteria andOrdMemoIsNull() {
            addCriterion("ord_memo is null");
            return (Criteria) this;
        }

        public Criteria andOrdMemoIsNotNull() {
            addCriterion("ord_memo is not null");
            return (Criteria) this;
        }

        public Criteria andOrdMemoEqualTo(String value) {
            addCriterion("ord_memo =", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotEqualTo(String value) {
            addCriterion("ord_memo <>", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoGreaterThan(String value) {
            addCriterion("ord_memo >", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoGreaterThanOrEqualTo(String value) {
            addCriterion("ord_memo >=", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLessThan(String value) {
            addCriterion("ord_memo <", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLessThanOrEqualTo(String value) {
            addCriterion("ord_memo <=", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLike(String value) {
            addCriterion("ord_memo like", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotLike(String value) {
            addCriterion("ord_memo not like", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoIn(List<String> values) {
            addCriterion("ord_memo in", values, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotIn(List<String> values) {
            addCriterion("ord_memo not in", values, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoBetween(String value1, String value2) {
            addCriterion("ord_memo between", value1, value2, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotBetween(String value1, String value2) {
            addCriterion("ord_memo not between", value1, value2, "ordMemo");
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


        public Criteria andStatusIsNull() {
            addCriterion("status is null");
            return (Criteria) this;
        }

        public Criteria andStatusIsNotNull() {
            addCriterion("status is not null");
            return (Criteria) this;
        }

        public Criteria andStatusEqualTo(Integer value) {
            addCriterion("status =", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotEqualTo(Integer value) {
            addCriterion("status <>", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusGreaterThan(Integer value) {
            addCriterion("status >", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusGreaterThanOrEqualTo(Integer value) {
            addCriterion("status >=", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLessThan(Integer value) {
            addCriterion("status <", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLessThanOrEqualTo(Integer value) {
            addCriterion("status <=", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLike(Integer value) {
            addCriterion("status like", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotLike(Integer value) {
            addCriterion("status not like", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusIn(List<Integer> values) {
            addCriterion("status in", values, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotIn(List<Integer> values) {
            addCriterion("status not in", values, "status");
            return (Criteria) this;
        }

        public Criteria andStatusBetween(Integer value1, Integer value2) {
            addCriterion("status between", value1, value2, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotBetween(Integer value1, Integer value2) {
            addCriterion("status not between", value1, value2, "status");
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
