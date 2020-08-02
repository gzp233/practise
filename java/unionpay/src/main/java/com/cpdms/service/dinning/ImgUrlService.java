package com.cpdms.service.dinning;

import java.io.IOException;
import java.util.List;

import com.cpdms.util.ImgCalcUtils;
import net.coobird.thumbnailator.Thumbnails;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.util.ClassUtils;
import org.springframework.web.multipart.MultipartFile;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.conf.V2Config;
import com.cpdms.common.file.FileUploadUtils;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.ImgUrlMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.dinning.ImgUrl;
import com.cpdms.model.dinning.ImgUrlExample;
import com.cpdms.util.SnowflakeIdWorker;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;

import cn.hutool.core.io.FileUtil;

/**
 * 图片路径 ImgUrlService
 * @Title: ImgUrlService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 13:24:45  
 **/
@Service
public class ImgUrlService implements BaseService<ImgUrl, ImgUrlExample>{
	private static Logger logger = LoggerFactory.getLogger(ImgUrlService.class);
	@Autowired
	private ImgUrlMapper imgUrlMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<ImgUrl> list(Tablepar tablepar,String name){
	        ImgUrlExample testExample=new ImgUrlExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andImgIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<ImgUrl> list= imgUrlMapper.selectByExample(testExample);
	        PageInfo<ImgUrl> pageInfo = new PageInfo<ImgUrl>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			ImgUrlExample example=new ImgUrlExample();
			example.createCriteria().andIdIn(lista);
			return imgUrlMapper.deleteByExample(example);


	}


	@Override
	public ImgUrl selectByPrimaryKey(String id) {

			return imgUrlMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(ImgUrl record) {
		return imgUrlMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(ImgUrl record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return imgUrlMapper.insertSelective(record);
	}


	/**
	 * 文件上传文件存储到文件表
	 * @param file
	 * @param fileURL
	 * @return 主键
	 * @throws IOException
	 */
	public String insertSelective(MultipartFile file) throws IOException {
		//文件上传获取文件名字
        String files = FileUploadUtils.upload(file);
        //补充完整url地址,生成缩略图
        String filesURL="";
        String thumbURL="";
        if ("Y".equals(V2Config.getIsstatic())) {
        	filesURL=V2Config.getIsroot_dir()+files;
        	thumbURL = V2Config.getIsroot_dir() + "thumb_" + files;
        	Thumbnails.of(FileUploadUtils.getRoot_dir() + files).scale(0.2f).toFile(FileUploadUtils.getRoot_dir() + "thumb_" + files);
		}else {
			filesURL=V2Config.getProfile()+files;
			thumbURL = V2Config.getProfile() + "thumb_" + files;
			Thumbnails.of(FileUploadUtils.getDefaultBaseDir() + files).scale(0.2f).toFile(FileUploadUtils.getDefaultBaseDir() + "thumb_" + files);
		}
        String img_url_path = V2Config.getImg_url_path();
        filesURL = img_url_path + filesURL;
        thumbURL = img_url_path + thumbURL;
		ImgUrl record=new ImgUrl();
		//添加雪花主键id
		record.setId(SnowflakeIdWorker.getUUID());
		record.setImgPath(filesURL);
		record.setThumbImgPath(thumbURL);
		if(imgUrlMapper.insertSelective(record)>0)
		{
			return record.getId();
		}
		return null;
	}

	/**
	 * 文件上传文件存储到文件表
	 * @param file
	 * @param fileURL
	 * @return 主键
	 * @throws IOException
	 */
	public String insertSelective2(MultipartFile file) throws IOException {
		//文件上传获取文件名字
		String files = FileUploadUtils.upload(FileUploadUtils.getPersonnel_dir(), file);
		return V2Config.getPersonnel_dir() + files;
	}

	/**
	 * 文件上传文件存储到文件表
	 * @param file
	 * @param fileURL
	 * @return 主键
	 */
	public String insertSelective3(MultipartFile file) {
		//文件上传获取文件名字
		String files = "";
		try {
			files = FileUploadUtils.upload(FileUploadUtils.getPersonnel_dir(), file, FileUploadUtils.FILE_ZIP_EXTENSION);
		}
		catch (Exception e)
		{
			e.printStackTrace();
			return "";
		}

		return V2Config.getPersonnel_dir() + files;
	}

	/**
	 * 删除文件存储表以及数据库
	 * @param ids 文件集合 1,2,3
	 */
	public int deleteBydataFile(String ids) {
		List<String> lista=Convert.toListStrArray(ids);
		//删除本地文件
		ImgUrlExample example=new ImgUrlExample();
		example.createCriteria().andIdIn(lista);
		List<ImgUrl> datas=imgUrlMapper.selectByExample(example);

		for (ImgUrl imgUrl : datas) {
			deletefile(imgUrl.getImgPath());
			//删除文件存储表
			imgUrlMapper.deleteByPrimaryKey(imgUrl.getId());
		}
		//删除数据库
		return imgUrlMapper.deleteByExample(example);
	}

	@Override
	public int updateByExampleSelective(ImgUrl record, ImgUrlExample example) {

		return imgUrlMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(ImgUrl record, ImgUrlExample example) {

		return imgUrlMapper.updateByExample(record, example);
	}

	@Override
	public List<ImgUrl> selectByExample(ImgUrlExample example) {

		return imgUrlMapper.selectByExample(example);
	}


	@Override
	public long countByExample(ImgUrlExample example) {

		return imgUrlMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(ImgUrlExample example) {

		return imgUrlMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param imgUrl
	 * @return
	 */
	public int checkNameUnique(ImgUrl imgUrl){
		ImgUrlExample example=new ImgUrlExample();
		example.createCriteria().andImgIdEqualTo(imgUrl.getImgId());
		List<ImgUrl> list=imgUrlMapper.selectByExample(example);
		return list.size();
	}

	/**
	 *删除本地文件方法
	 */
	public void deletefile(String filePath) {
		if("Y".equals(V2Config.getIsstatic())) {
			String url=ClassUtils.getDefaultClassLoader().getResource("").getPath()+filePath;

			FileUtil.del(url);
		}else {
			FileUtil.del(filePath);
		}


	}
}
