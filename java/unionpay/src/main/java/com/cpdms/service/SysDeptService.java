package com.cpdms.service;

import com.cpdms.mapper.auto.TSysDeptMapper;
import com.cpdms.model.auto.TSysDept;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Lazy;
import org.springframework.stereotype.Service;

import javax.annotation.Resource;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Service
public class SysDeptService {
    @Resource
    @Lazy
    private TSysDeptMapper sysDeptMapper;

    /**
     *
     * @param params
     * @return
     */
    public  List<TSysDept> queryDeptList(Map params){
        return sysDeptMapper.queryDeptList(params);
    }

    /**
     *
     * @param parentId
     * @return
     */
        public List<String> querySubDeptIds(String parentId){
            return sysDeptMapper.querySubDeptIds(parentId);
    }

    /**
     *
     * @param sysDept
     */
    void saveDept(TSysDept sysDept){
        sysDeptMapper.insertDept(sysDept);
    }

    /**
     * 递归查询所有子部门ID
     * @param deptId
     * @return
     */
    public List<String> getSubDeptIdList(String deptId){
        //部门及子部门ID列表
        List<String> deptIdList = new ArrayList<>();
        //获取子部门ID
        List<String> subIdList = querySubDeptIds(deptId);
        getDeptTreeList(subIdList, deptIdList);

        return deptIdList;
    }


    /**
     *
     * @param subIdList
     * @param deptIdList
     */
    private void getDeptTreeList(List<String> subIdList, List<String> deptIdList){
        for(String deptId : subIdList){
            List<String> list = querySubDeptIds(deptId);
            if(list.size() > 0){
                getDeptTreeList(list, deptIdList);
            }
            deptIdList.add(deptId);
        }
    }
}
