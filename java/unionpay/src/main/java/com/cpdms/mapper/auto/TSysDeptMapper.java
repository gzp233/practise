package com.cpdms.mapper.auto;

import com.cpdms.model.auto.TSysDept;

import java.util.List;
import java.util.Map;

public interface TSysDeptMapper {

List<TSysDept> queryDeptList(Map params);

List<String> querySubDeptIds(String parentId);

void insertDept(TSysDept sysDept);

}
